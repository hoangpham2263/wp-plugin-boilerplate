/**
 * QHW Plugin Features - Main JavaScript File
 * 
 * This file contains JavaScript functionality for all features
 * Now using vanilla JavaScript instead of jQuery
 */

(function() {
    'use strict';
    
    // QHW Features namespace
    window.QHWFeatures = window.QHWFeatures || {};
    
    // Utility functions
    function $(selector) {
        return document.querySelectorAll(selector);
    }
    
    function addEvent(element, event, handler) {
        if (element.addEventListener) {
            element.addEventListener(event, handler, false);
        } else if (element.attachEvent) {
            element.attachEvent('on' + event, handler);
        }
    }
    
    function removeClass(element, className) {
        if (element.classList) {
            element.classList.remove(className);
        } else {
            element.className = element.className.replace(new RegExp('(^|\\b)' + className.split(' ').join('|') + '(\\b|$)', 'gi'), ' ');
        }
    }
    
    function addClass(element, className) {
        if (element.classList) {
            element.classList.add(className);
        } else {
            element.className += ' ' + className;
        }
    }
    
    function hasClass(element, className) {
        if (element.classList) {
            return element.classList.contains(className);
        } else {
            return new RegExp('(^| )' + className + '( |$)', 'gi').test(element.className);
        }
    }
    
    function fadeOut(element, duration, callback) {
        element.style.opacity = '1';
        element.style.transition = 'opacity ' + duration + 'ms ease-in-out';
        
        setTimeout(function() {
            element.style.opacity = '0';
            setTimeout(function() {
                element.style.display = 'none';
                if (callback) callback();
            }, duration);
        }, 10);
    }
    
    function fadeIn(element, duration) {
        element.style.opacity = '0';
        element.style.display = 'block';
        element.style.transition = 'opacity ' + duration + 'ms ease-in-out';
        
        setTimeout(function() {
            element.style.opacity = '1';
        }, 10);
    }
    
    function hide(element) {
        element.style.display = 'none';
    }
    
    function show(element) {
        element.style.display = 'block';
    }
    
    function text(element, newText) {
        if (newText !== undefined) {
            element.textContent = newText;
        } else {
            return element.textContent;
        }
    }
    
    function prop(element, property, value) {
        if (value !== undefined) {
            element[property] = value;
        } else {
            return element[property];
        }
    }
    
    function data(element, key, value) {
        if (value !== undefined) {
            element.dataset[key] = value;
        } else {
            return element.dataset[key];
        }
    }
    
    function attr(element, attribute, value) {
        if (value !== undefined) {
            element.setAttribute(attribute, value);
        } else {
            return element.getAttribute(attribute);
        }
    }
    
    function html(element, content) {
        if (content !== undefined) {
            element.innerHTML = content;
        } else {
            return element.innerHTML;
        }
    }
    
    function each(elements, callback) {
        Array.prototype.forEach.call(elements, callback);
    }
    
    function find(element, selector) {
        return element.querySelectorAll(selector);
    }
    
    function closest(element, selector) {
        while (element && !element.matches(selector)) {
            element = element.parentElement;
        }
        return element;
    }
    
    function matches(element, selector) {
        if (element.matches) {
            return element.matches(selector);
        } else if (element.msMatchesSelector) {
            return element.msMatchesSelector(selector);
        } else if (element.webkitMatchesSelector) {
            return element.webkitMatchesSelector(selector);
        }
        return false;
    }
    
    function trigger(element, eventName) {
        var event = new Event(eventName, { bubbles: true, cancelable: true });
        element.dispatchEvent(event);
    }
    
    function on(element, event, selector, handler) {
        if (typeof selector === 'function') {
            // Direct event binding
            addEvent(element, event, selector);
        } else {
            // Delegated event binding
            addEvent(element, event, function(e) {
                var target = e.target;
                while (target && target !== element) {
                    if (matches(target, selector)) {
                        handler.call(target, e);
                        break;
                    }
                    target = target.parentElement;
                }
            });
        }
    }
    
    function width(element) {
        return element.offsetWidth;
    }
    
    function resize(callback) {
        addEvent(window, 'resize', callback);
    }
    
    function ready(callback) {
        if (document.readyState === 'loading') {
            addEvent(document, 'DOMContentLoaded', callback);
        } else {
            callback();
        }
    }
    
    function setTimeout(callback, delay) {
        return window.setTimeout(callback, delay);
    }
    
    /**
     * Initialize features when DOM is ready
     */
    ready(function() {
        QHWFeatures.init();
        
        // Get current tab from URL or default to tab1
        var currentTab = getUrlParameter('tab') || 'tab1';
        console.log('Initializing with tab:', currentTab);
        
        // Ensure correct tab is visible on page load
        each($('.qhw-tab-content'), function(element) {
            removeClass(element, 'active');
            hide(element);
        });
        
        var targetSelector = '.qhw-tab-content[data-tab="' + currentTab + '"]';
        var targetElements = $(targetSelector);
        console.log('Target selector:', targetSelector);
        console.log('Found elements:', targetElements.length);
        
        if (targetElements.length > 0) {
            addClass(targetElements[0], 'active');
            show(targetElements[0]);
        } else {
            console.error('Tab content not found on page load:', targetSelector);
            // Fallback: show first tab if target not found
            var firstTab = $('.qhw-tab-content')[0];
            if (firstTab) {
                addClass(firstTab, 'active');
                show(firstTab);
            }
        }
        
        each($('.qhw-tab-navigation .nav-tab'), function(element) {
            removeClass(element, 'nav-tab-active');
        });
        
        var activeTab = $('.qhw-tab-navigation .nav-tab[data-tab="' + currentTab + '"]')[0];
        if (activeTab) {
            addClass(activeTab, 'nav-tab-active');
        }
        
        // Debug tab system
        setTimeout(function() {
            QHWFeatures.debugTabSystem();
        }, 100);
    });
    
    /**
     * Main initialization function
     */
    QHWFeatures.init = function() {
        this.initTabSystem();
        this.initFeatureCards();
        this.initInteractiveElements();
        this.initResponsiveBehavior();
    };
    
    /**
     * Initialize tab system functionality
     */
    QHWFeatures.initTabSystem = function() {
        // Tab switching with smooth transitions
        each($('.qhw-tab-navigation .nav-tab'), function(element) {
            on(element, 'click', function(e) {
                e.preventDefault();
                
                var targetTab = data(this, 'tab');
                console.log('Tab clicked:', targetTab); // Debug log
                
                // Update active tab
                each($('.qhw-tab-navigation .nav-tab'), function(tabElement) {
                    removeClass(tabElement, 'nav-tab-active');
                });
                addClass(this, 'nav-tab-active');
                
                // Get target tab content
                var targetContentSelector = '.qhw-tab-content[data-tab="' + targetTab + '"]';
                var targetContentElements = $(targetContentSelector);
                var currentActiveElements = $('.qhw-tab-content.active');
                
                if (targetContentElements.length > 0) {
                    var targetContent = targetContentElements[0];
                    
                    // If clicking the same tab, do nothing
                    if (hasClass(targetContent, 'active')) {
                        return;
                    }
                    
                    // Smooth transition: fade out current, fade in target
                    if (currentActiveElements.length > 0) {
                        var currentActive = currentActiveElements[0];
                        removeClass(currentActive, 'active');
                        fadeOut(currentActive, 150, function() {
                            addClass(targetContent, 'active');
                            fadeIn(targetContent, 150);
                        });
                    } else {
                        // No current active tab, just show target
                        each($('.qhw-tab-content'), function(tabContent) {
                            removeClass(tabContent, 'active');
                            hide(tabContent);
                        });
                        addClass(targetContent, 'active');
                        fadeIn(targetContent, 150);
                    }
                } else {
                    console.error('Tab content not found:', targetTab);
                }
            });
        });
        
        // Handle browser back/forward buttons
        on(window, 'popstate', function() {
            var currentTab = getUrlParameter('tab') || 'tab1';
            var targetTab = $('.qhw-tab-navigation .nav-tab[data-tab="' + currentTab + '"]')[0];
            if (targetTab) {
                trigger(targetTab, 'click');
            }
        });
    };
    
    /**
     * Initialize feature cards functionality
     */
    QHWFeatures.initFeatureCards = function() {
        // Feature card interactions
        each($('.qhw-feature-card'), function(card) {
            // Card hover effects
            on(card, 'mouseenter', function() {
                addClass(this, 'hover');
            });
            
            on(card, 'mouseleave', function() {
                removeClass(this, 'hover');
            });
            
            // Card click to expand
            on(card, 'click', function() {
                if (!hasClass(this, 'expanded')) {
                    addClass(this, 'expanded');
                } else {
                    removeClass(this, 'expanded');
                }
            });
        });
    };
    
    /**
     * Initialize interactive elements
     */
    QHWFeatures.initInteractiveElements = function() {
        // Demo buttons
        each($('.qhw-demo-button'), function(button) {
            on(button, 'click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                var originalText = text(this);
                
                // Show loading state
                prop(this, 'disabled', true);
                text(this, 'Loading...');
                
                // Simulate action (replace with actual functionality)
                setTimeout(function() {
                    prop(button, 'disabled', false);
                    text(button, originalText);
                    
                    // Show success message in console
                    console.log('Demo action completed successfully!');
                }, 1000);
            });
        });
        
        // Feature info toggles
        each($('.qhw-feature-info h5'), function(heading) {
            on(heading, 'click', function() {
                var info = this.nextElementSibling;
                if (info && (info.tagName === 'UL' || info.tagName === 'OL')) {
                    if (info.style.display === 'none' || info.style.display === '') {
                        info.style.display = 'block';
                    } else {
                        info.style.display = 'none';
                    }
                }
            });
        });
    };
    
    /**
     * Initialize responsive behavior
     */
    QHWFeatures.initResponsiveBehavior = function() {
        // Handle window resize
        resize(function() {
            QHWFeatures.adjustLayout();
        });
        
        // Initial layout adjustment
        QHWFeatures.adjustLayout();
    };
    
    /**
     * Adjust layout based on screen size
     */
    QHWFeatures.adjustLayout = function() {
        var windowWidth = width(window);
        
        each($('.qhw-feature-card'), function(card) {
            if (windowWidth <= 782) {
                // Mobile layout adjustments
                addClass(card, 'mobile-layout');
            } else {
                // Desktop layout
                removeClass(card, 'mobile-layout');
            }
        });
        
        each($('.qhw-feature-actions .button'), function(button) {
            if (windowWidth <= 782) {
                addClass(button, 'mobile-button');
            } else {
                removeClass(button, 'mobile-button');
            }
        });
    };
    
    /**
     * Debug function to check tab system status
     */
    QHWFeatures.debugTabSystem = function() {
        console.log('=== Tab System Debug ===');
        
        var activeTab = $('.qhw-tab-navigation .nav-tab-active')[0];
        if (activeTab) {
            console.log('Active tab:', data(activeTab, 'tab'));
        }
        
        var activeContent = $('.qhw-tab-content.active')[0];
        if (activeContent) {
            console.log('Active content:', data(activeContent, 'tab'));
        }
        
        console.log('All tab content elements:', $('.qhw-tab-content').length);
        console.log('Tab navigation elements:', $('.qhw-tab-navigation .nav-tab').length);
        
        each($('.qhw-tab-content'), function(element) {
            console.log('Tab content:', data(element, 'tab'), 'Active:', hasClass(element, 'active'), 'Visible:', element.style.display !== 'none');
        });
        
        each($('.qhw-tab-navigation .nav-tab'), function(element) {
            console.log('Tab nav:', data(element, 'nav'), 'Active:', hasClass(element, 'nav-tab-active'));
        });
    };
    
    /**
     * Utility function to get URL parameters
     */
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }
    
    /**
     * Public API for external use
     */
    QHWFeatures.publicAPI = {
        showTab: function(tabName) {
            var tabSelector = '.qhw-tab-navigation .nav-tab[href*="tab=' + tabName + '"]';
            var tabElements = $(tabSelector);
            if (tabElements.length > 0) {
                trigger(tabElements[0], 'click');
            }
        },
        
        refreshFeatures: function() {
            location.reload();
        }
    };
    
})();
