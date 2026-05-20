tailwind.config = {
    darkMode: "class",
    theme: {
        extend: {
            "colors": {
                "secondary": "#8B5CF6",
                "on-secondary": "#FFFFFF",
                "secondary-container": "#7C3AED",
                "on-secondary-container": "#EDE9FE",
                "secondary-fixed": "#312E81",
                "secondary-fixed-dim": "#6D28D9",
                "on-secondary-fixed": "#EDE9FE",
                "on-secondary-fixed-variant": "#C4B5FD",
                "primary": "#F1F5F9",
                "on-primary": "#0F172A",
                "primary-container": "#1E293B",
                "on-primary-container": "#C4B5FD",
                "primary-fixed": "#C4B5FD",
                "primary-fixed-dim": "#8B5CF6",
                "on-primary-fixed": "#0F172A",
                "on-primary-fixed-variant": "#7C3AED",
                "tertiary": "#06B6D4",
                "on-tertiary": "#0F172A",
                "tertiary-container": "#164E63",
                "on-tertiary-container": "#A5F3FC",
                "tertiary-fixed": "#A5F3FC",
                "tertiary-fixed-dim": "#22D3EE",
                "on-tertiary-fixed": "#0F172A",
                "on-tertiary-fixed-variant": "#0891B2",
                "surface": "#1E293B",
                "surface-dim": "#0D1729",
                "surface-bright": "#263248",
                "surface-variant": "#334155",
                "surface-container-lowest": "#0A1120",
                "surface-container-low": "#162032",
                "surface-container": "#1E293B",
                "surface-container-high": "#263248",
                "surface-container-highest": "#2D3A52",
                "on-surface": "#E2E8F0",
                "on-surface-variant": "#94A3B8",
                "outline": "#475569",
                "outline-variant": "#334155",
                "background": "#0F172A",
                "on-background": "#E2E8F0",
                "inverse-surface": "#E2E8F0",
                "inverse-on-surface": "#1E293B",
                "inverse-primary": "#7C3AED",
                "error": "#F87171",
                "on-error": "#0F172A",
                "error-container": "#450A0A",
                "on-error-container": "#FCA5A5",
                "surface-tint": "#8B5CF6",
                "accent-cyan": "#06B6D4"
            },
            "borderRadius": {
                "DEFAULT": "0.5rem",
                "lg": "0.75rem",
                "xl": "1rem",
                "2xl": "1.5rem",
                "3xl": "2rem",
                "full": "9999px"
            },
            "spacing": {
                "container-margin": "32px",
                "card-padding": "24px",
                "sidebar-width": "280px",
                "unit": "8px",
                "gutter": "24px"
            },
            "fontFamily": {
                "headline-lg": ["Inter", "Plus Jakarta Sans"],
                "body-sm": ["Inter"],
                "title-md": ["Inter", "Plus Jakarta Sans"],
                "headline-lg-mobile": ["Inter", "Plus Jakarta Sans"],
                "display-lg": ["Inter", "Plus Jakarta Sans"],
                "label-caps": ["Inter"],
                "body-md": ["Inter"]
            },
            "fontSize": {
                "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "700"}],
                "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                "title-md": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                "headline-lg-mobile": ["24px", {"lineHeight": "32px", "fontWeight": "700"}],
                "display-lg": ["56px", {"lineHeight": "64px", "letterSpacing": "-0.03em", "fontWeight": "800"}],
                "label-caps": ["12px", {"lineHeight": "16px", "letterSpacing": "0.08em", "fontWeight": "600"}],
                "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}]
            },
            "boxShadow": {
                "glow-violet": "0 0 20px rgba(139,92,246,0.35)",
                "glow-cyan": "0 0 20px rgba(6,182,212,0.35)",
                "card": "0 4px 24px rgba(0,0,0,0.4)",
                "card-hover": "0 8px 40px rgba(0,0,0,0.5)"
            }
        }
    }
}
