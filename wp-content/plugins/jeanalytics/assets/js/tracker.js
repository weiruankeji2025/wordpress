/**
 * JE Analytics - 前端访客追踪脚本
 * Version: 1.0.0
 */

(function() {
    'use strict';

    // 配置
    const config = window.jeaConfig || {};
    const pageData = window.jeaPageData || {};

    // 状态
    let state = {
        visitorId: null,
        sessionId: null,
        pageLoadTime: Date.now(),
        lastActivityTime: Date.now(),
        scrollDepthMax: 0,
        scrollMarks: {},
        isVisible: true,
        heartbeatTimer: null,
        engaged: false
    };

    // 工具函数
    const utils = {
        // 生成UUID
        generateUUID: function() {
            return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
                const r = Math.random() * 16 | 0;
                const v = c === 'x' ? r : (r & 0x3 | 0x8);
                return v.toString(16);
            });
        },

        // 获取或创建访客ID
        getVisitorId: function() {
            let visitorId = localStorage.getItem('jea_visitor_id');
            if (!visitorId) {
                visitorId = this.generateUUID();
                localStorage.setItem('jea_visitor_id', visitorId);
            }
            return visitorId;
        },

        // 获取或创建会话ID
        getSessionId: function() {
            const sessionTimeout = (config.sessionTimeout || 30) * 60 * 1000;
            let session = JSON.parse(sessionStorage.getItem('jea_session') || 'null');

            if (!session || (Date.now() - session.lastActivity > sessionTimeout)) {
                session = {
                    id: this.generateUUID(),
                    startTime: Date.now(),
                    lastActivity: Date.now(),
                    pageviews: 0,
                    isNew: true
                };
            } else {
                session.isNew = false;
            }

            session.lastActivity = Date.now();
            session.pageviews++;
            sessionStorage.setItem('jea_session', JSON.stringify(session));

            return session;
        },

        // 获取屏幕信息
        getScreenInfo: function() {
            return {
                width: window.screen.width,
                height: window.screen.height,
                colorDepth: window.screen.colorDepth,
                pixelRatio: window.devicePixelRatio || 1,
                viewportWidth: window.innerWidth,
                viewportHeight: window.innerHeight
            };
        },

        // 获取时区
        getTimezone: function() {
            try {
                return Intl.DateTimeFormat().resolvedOptions().timeZone;
            } catch (e) {
                return '';
            }
        },

        // 获取语言
        getLanguage: function() {
            return navigator.language || navigator.userLanguage || '';
        },

        // 获取滚动深度百分比
        getScrollDepth: function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollHeight = document.documentElement.scrollHeight;
            const clientHeight = document.documentElement.clientHeight;
            const maxScroll = scrollHeight - clientHeight;

            if (maxScroll <= 0) return 100;

            return Math.min(100, Math.round((scrollTop / maxScroll) * 100));
        },

        // 发送数据
        sendData: function(action, data, callback) {
            const payload = new FormData();
            payload.append('action', 'jea_' + action);
            payload.append('nonce', config.nonce);
            payload.append('data', JSON.stringify(data));

            // 使用sendBeacon优先(适合页面卸载时发送)
            if (navigator.sendBeacon && action === 'track_exit') {
                const url = config.ajaxUrl + '?action=jea_' + action + '&nonce=' + config.nonce;
                navigator.sendBeacon(url, payload);
                return;
            }

            fetch(config.ajaxUrl, {
                method: 'POST',
                body: payload,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (callback) callback(null, data);
            })
            .catch(error => {
                if (callback) callback(error);
            });
        },

        // 获取来源域名
        getReferrerDomain: function() {
            if (!pageData.referrer) return '';
            try {
                const url = new URL(pageData.referrer);
                return url.hostname;
            } catch (e) {
                return '';
            }
        },

        // 检查是否是新访客
        isNewVisitor: function() {
            return localStorage.getItem('jea_returning') !== 'true';
        },

        // 标记为回访访客
        markAsReturning: function() {
            localStorage.setItem('jea_returning', 'true');
        }
    };

    // 追踪器
    const tracker = {
        // 初始化
        init: function() {
            state.visitorId = utils.getVisitorId();
            const session = utils.getSessionId();
            state.sessionId = session.id;

            // 初始化滚动标记
            (config.scrollDepthMarks || [25, 50, 75, 100]).forEach(mark => {
                state.scrollMarks[mark] = false;
            });

            // 追踪页面访问
            this.trackPageview(session.isNew, session.pageviews === 1);

            // 绑定事件
            this.bindEvents();

            // 启动心跳
            this.startHeartbeat();

            // 标记为回访访客
            utils.markAsReturning();
        },

        // 追踪页面访问
        trackPageview: function(isNewSession, isEntryPage) {
            const screen = utils.getScreenInfo();

            const data = {
                visitorId: state.visitorId,
                sessionId: state.sessionId,
                pageUrl: pageData.pageUrl || window.location.href,
                pageTitle: pageData.pageTitle || document.title,
                pageType: pageData.pageType || 'other',
                postId: pageData.postId || 0,
                referrer: pageData.referrer || document.referrer,
                referrerDomain: utils.getReferrerDomain(),
                isNewSession: isNewSession,
                isEntryPage: isEntryPage,
                isNewVisitor: utils.isNewVisitor(),
                screenWidth: screen.width,
                screenHeight: screen.height,
                viewportWidth: screen.viewportWidth,
                viewportHeight: screen.viewportHeight,
                colorDepth: screen.colorDepth,
                pixelRatio: screen.pixelRatio,
                language: utils.getLanguage(),
                timezone: utils.getTimezone(),
                utmSource: pageData.utm_source || '',
                utmMedium: pageData.utm_medium || '',
                utmCampaign: pageData.utm_campaign || '',
                utmTerm: pageData.utm_term || '',
                utmContent: pageData.utm_content || '',
                timestamp: Date.now()
            };

            utils.sendData('track_pageview', data);
        },

        // 绑定事件
        bindEvents: function() {
            // 滚动追踪
            let scrollTimeout;
            window.addEventListener('scroll', () => {
                clearTimeout(scrollTimeout);
                scrollTimeout = setTimeout(() => {
                    this.trackScroll();
                }, 100);
            }, { passive: true });

            // 页面可见性变化
            document.addEventListener('visibilitychange', () => {
                state.isVisible = document.visibilityState === 'visible';
                if (state.isVisible) {
                    state.lastActivityTime = Date.now();
                }
            });

            // 用户活动
            ['click', 'keydown', 'mousemove', 'touchstart'].forEach(event => {
                document.addEventListener(event, () => {
                    state.lastActivityTime = Date.now();
                    if (!state.engaged) {
                        state.engaged = true;
                        this.trackEvent('engagement', 'engaged');
                    }
                }, { passive: true, once: event !== 'click' });
            });

            // 点击追踪
            document.addEventListener('click', (e) => {
                this.trackClick(e);
            });

            // 页面卸载
            window.addEventListener('beforeunload', () => {
                this.trackExit();
            });

            // 页面隐藏(移动端)
            window.addEventListener('pagehide', () => {
                this.trackExit();
            });

            // 外链点击
            document.addEventListener('click', (e) => {
                const link = e.target.closest('a');
                if (link && link.hostname !== window.location.hostname) {
                    this.trackEvent('outbound', 'click', link.href);
                }
            });
        },

        // 追踪滚动
        trackScroll: function() {
            const depth = utils.getScrollDepth();

            if (depth > state.scrollDepthMax) {
                state.scrollDepthMax = depth;

                // 检查里程碑
                Object.keys(state.scrollMarks).forEach(mark => {
                    if (depth >= parseInt(mark) && !state.scrollMarks[mark]) {
                        state.scrollMarks[mark] = true;
                        this.trackEvent('scroll', 'depth', mark + '%');
                    }
                });
            }
        },

        // 追踪点击
        trackClick: function(e) {
            const target = e.target;

            // 追踪按钮点击
            if (target.closest('button, .button, .btn, [role="button"]')) {
                const button = target.closest('button, .button, .btn, [role="button"]');
                const label = button.innerText || button.getAttribute('aria-label') || 'button';
                this.trackEvent('click', 'button', label.substring(0, 100));
            }

            // 追踪下载链接
            const link = target.closest('a');
            if (link) {
                const href = link.getAttribute('href') || '';
                const downloadExtensions = ['.pdf', '.doc', '.docx', '.xls', '.xlsx', '.zip', '.rar', '.mp3', '.mp4'];

                downloadExtensions.forEach(ext => {
                    if (href.toLowerCase().includes(ext)) {
                        this.trackEvent('download', ext.replace('.', ''), href);
                    }
                });

                // 追踪电话链接
                if (href.startsWith('tel:')) {
                    this.trackEvent('click', 'phone', href.replace('tel:', ''));
                }

                // 追踪邮件链接
                if (href.startsWith('mailto:')) {
                    this.trackEvent('click', 'email', href.replace('mailto:', '').split('?')[0]);
                }
            }
        },

        // 追踪事件
        trackEvent: function(category, action, label, value) {
            const data = {
                visitorId: state.visitorId,
                sessionId: state.sessionId,
                pageUrl: window.location.href,
                category: category,
                action: action,
                label: label || '',
                value: value || 0,
                timestamp: Date.now()
            };

            utils.sendData('track_event', data);
        },

        // 追踪退出
        trackExit: function() {
            const timeOnPage = Math.round((Date.now() - state.pageLoadTime) / 1000);

            const data = {
                visitorId: state.visitorId,
                sessionId: state.sessionId,
                pageUrl: window.location.href,
                timeOnPage: timeOnPage,
                scrollDepth: state.scrollDepthMax,
                engaged: state.engaged,
                timestamp: Date.now()
            };

            utils.sendData('track_exit', data);
        },

        // 心跳
        startHeartbeat: function() {
            const interval = (config.heartbeatInterval || 15) * 1000;

            state.heartbeatTimer = setInterval(() => {
                if (state.isVisible) {
                    const data = {
                        visitorId: state.visitorId,
                        sessionId: state.sessionId,
                        pageUrl: window.location.href,
                        pageTitle: document.title,
                        timestamp: Date.now()
                    };

                    utils.sendData('heartbeat', data);
                }
            }, interval);
        }
    };

    // 公开API
    window.JEAnalytics = {
        trackEvent: function(category, action, label, value) {
            tracker.trackEvent(category, action, label, value);
        },

        trackPageview: function(url, title) {
            pageData.pageUrl = url || window.location.href;
            pageData.pageTitle = title || document.title;
            tracker.trackPageview(false, false);
        },

        getVisitorId: function() {
            return state.visitorId;
        },

        getSessionId: function() {
            return state.sessionId;
        }
    };

    // 页面加载完成后初始化
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            tracker.init();
        });
    } else {
        tracker.init();
    }

})();
