document.querySelectorAll('[data-print]').forEach((button) => {
    button.addEventListener('click', () => {
        window.print();
    });
});

let activeRichEditor = null;

document.querySelectorAll('[data-rich-editor]').forEach((editor) => {
    const surface = editor.querySelector('[data-editor-surface]');
    const source = editor.querySelector('[data-editor-source]');
    const toggle = editor.querySelector('[data-editor-toggle]');

    if (!surface || !source || !toggle) {
        return;
    }

    const syncSource = () => {
        source.value = surface.innerHTML.trim();
    };

    const syncSurface = () => {
        surface.innerHTML = source.value;
    };

    const focusSurface = () => {
        activeRichEditor = editor;
        surface.focus();
    };

    surface.addEventListener('focus', () => {
        activeRichEditor = editor;
    });

    surface.addEventListener('input', syncSource);
    source.addEventListener('input', syncSurface);

    toggle.addEventListener('click', () => {
        const sourceMode = editor.classList.toggle('is-source');

        if (sourceMode) {
            syncSource();
            toggle.textContent = 'Plain Edit';
            source.focus();
            return;
        }

        syncSurface();
        toggle.textContent = 'HTML Code';
        focusSurface();
    });

    editor.querySelectorAll('[data-command]').forEach((control) => {
        control.addEventListener('click', () => {
            if (control.tagName === 'SELECT' || control.type === 'color') {
                return;
            }

            focusSurface();
            document.execCommand(control.dataset.command, false, null);
            syncSource();
        });

        control.addEventListener('change', () => {
            if (!control.value) {
                return;
            }

            focusSurface();
            document.execCommand(control.dataset.command, false, control.value);
            syncSource();
            if (control.tagName === 'SELECT') {
                control.selectedIndex = 0;
            }
        });
    });

    editor.querySelectorAll('[data-create-link]').forEach((button) => {
        button.addEventListener('click', () => {
            const url = window.prompt('Enter link URL');

            if (!url) {
                return;
            }

            focusSurface();
            document.execCommand('createLink', false, url);
            syncSource();
        });
    });
});

document.querySelectorAll('[data-template-tag]').forEach((button) => {
    button.addEventListener('click', () => {
        const editor = activeRichEditor || document.querySelector('[data-rich-editor]');

        if (!editor) {
            return;
        }

        const surface = editor.querySelector('[data-editor-surface]');
        const source = editor.querySelector('[data-editor-source]');
        const tag = button.dataset.templateTag || '';

        if (editor.classList.contains('is-source')) {
            const start = source.selectionStart;
            const end = source.selectionEnd;
            source.value = source.value.slice(0, start) + tag + source.value.slice(end);
            source.dispatchEvent(new Event('input'));
            source.focus();
            source.selectionStart = source.selectionEnd = start + tag.length;
            return;
        }

        surface.focus();
        document.execCommand('insertText', false, tag);
        source.value = surface.innerHTML.trim();
    });
});

document.querySelectorAll('form').forEach((form) => {
    form.addEventListener('submit', () => {
        form.querySelectorAll('[data-rich-editor]').forEach((editor) => {
            const surface = editor.querySelector('[data-editor-surface]');
            const source = editor.querySelector('[data-editor-source]');

            if (!editor.classList.contains('is-source')) {
                source.value = surface.innerHTML.trim();
            }
        });
    });
});

const themePresetData = document.getElementById('ui-theme-presets-json');
const themeForm = document.querySelector('[data-ui-theme-form]');

if (themePresetData && themeForm) {
    let presets = {};

    try {
        presets = JSON.parse(themePresetData.textContent || '{}');
    } catch (error) {
        presets = {};
    }

    const cssVars = {
        app_background: '--smapis-app-bg',
        sidebar_background: '--smapis-sidebar-bg',
        primary_button_background: '--smapis-primary-btn-bg',
        primary_button_text: '--smapis-primary-btn-text',
        active_sidebar_background: '--smapis-active-sidebar-bg',
        active_sidebar_text: '--smapis-active-sidebar-text',
        inactive_sidebar_text: '--smapis-inactive-sidebar-text',
        topbar_background: '--smapis-topbar-bg',
        banner_background: '--smapis-banner-bg',
        card_background: '--smapis-card-bg',
    };

    const presetSelect = themeForm.querySelector('[data-theme-preset]');
    const hexPattern = /^#(?:[0-9a-fA-F]{3}|[0-9a-fA-F]{6})$/;

    const expandHex = (value) => {
        const clean = String(value || '').trim();

        if (!hexPattern.test(clean)) {
            return '';
        }

        if (clean.length === 4) {
            return `#${clean[1]}${clean[1]}${clean[2]}${clean[2]}${clean[3]}${clean[3]}`.toUpperCase();
        }

        return clean.toUpperCase();
    };

    const applyColor = (key, value) => {
        const normalized = expandHex(value);
        if (!normalized || !cssVars[key]) {
            return;
        }

        document.documentElement.style.setProperty(cssVars[key], normalized);
    };

    const setCustomPreset = () => {
        if (presetSelect && presetSelect.value !== 'custom') {
            presetSelect.value = 'custom';
        }
    };

    themeForm.querySelectorAll('[data-theme-color]').forEach((picker) => {
        picker.addEventListener('input', () => {
            const key = picker.dataset.themeColor;
            const hex = themeForm.querySelector(`[data-theme-hex="${key}"]`);
            const normalized = expandHex(picker.value);

            if (hex && normalized) {
                hex.value = normalized;
            }

            applyColor(key, picker.value);
            setCustomPreset();
        });
    });

    themeForm.querySelectorAll('[data-theme-hex]').forEach((input) => {
        input.addEventListener('input', () => {
            const key = input.dataset.themeHex;
            const normalized = expandHex(input.value);
            const picker = themeForm.querySelector(`[data-theme-color="${key}"]`);

            if (normalized) {
                if (picker) {
                    picker.value = normalized;
                }
                input.value = normalized;
                applyColor(key, normalized);
            }

            setCustomPreset();
        });
    });

    if (presetSelect) {
        presetSelect.addEventListener('change', () => {
            const preset = presets[presetSelect.value] || {};
            const colors = preset.colors || {};

            Object.keys(cssVars).forEach((key) => {
                if (!colors[key]) {
                    return;
                }

                const normalized = expandHex(colors[key]);
                const picker = themeForm.querySelector(`[data-theme-color="${key}"]`);
                const hex = themeForm.querySelector(`[data-theme-hex="${key}"]`);

                if (picker && normalized) {
                    picker.value = normalized;
                }
                if (hex && normalized) {
                    hex.value = normalized;
                }
                applyColor(key, normalized);
            });
        });
    }
}
