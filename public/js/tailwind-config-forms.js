tailwind.config = {
    theme: {
        extend: {
            "colors": {
                "secondary":                    "var(--tw-secondary)",
                "on-secondary":                 "var(--tw-on-secondary)",
                "secondary-container":          "var(--tw-secondary-container)",
                "on-secondary-container":       "var(--tw-on-secondary-container)",
                "secondary-fixed":              "var(--tw-secondary-fixed)",
                "secondary-fixed-dim":          "var(--tw-secondary-fixed-dim)",
                "on-secondary-fixed":           "var(--tw-on-secondary-fixed)",
                "on-secondary-fixed-variant":   "var(--tw-on-secondary-fixed-variant)",
                "primary":                      "var(--tw-primary)",
                "on-primary":                   "var(--tw-on-primary)",
                "primary-container":            "var(--tw-primary-container)",
                "on-primary-container":         "var(--tw-on-primary-container)",
                "primary-fixed":                "var(--tw-primary-fixed)",
                "primary-fixed-dim":            "var(--tw-primary-fixed-dim)",
                "on-primary-fixed":             "var(--tw-on-primary-fixed)",
                "on-primary-fixed-variant":     "var(--tw-on-primary-fixed-variant)",
                "inverse-primary":              "var(--tw-inverse-primary)",
                "tertiary":                     "var(--tw-tertiary)",
                "on-tertiary":                  "var(--tw-on-tertiary)",
                "tertiary-container":           "var(--tw-tertiary-container)",
                "on-tertiary-container":        "var(--tw-on-tertiary-container)",
                "tertiary-fixed":               "var(--tw-tertiary-fixed)",
                "tertiary-fixed-dim":           "var(--tw-tertiary-fixed-dim)",
                "on-tertiary-fixed":            "var(--tw-on-tertiary-fixed)",
                "on-tertiary-fixed-variant":    "var(--tw-on-tertiary-fixed-variant)",
                "surface":                      "var(--tw-surface)",
                "surface-dim":                  "var(--tw-surface-dim)",
                "surface-bright":               "var(--tw-surface-bright)",
                "surface-variant":              "var(--tw-surface-variant)",
                "surface-container-lowest":     "var(--tw-surface-container-lowest)",
                "surface-container-low":        "var(--tw-surface-container-low)",
                "surface-container":            "var(--tw-surface-container)",
                "surface-container-high":       "var(--tw-surface-container-high)",
                "surface-container-highest":    "var(--tw-surface-container-highest)",
                "on-surface":                   "var(--tw-on-surface)",
                "on-surface-variant":           "var(--tw-on-surface-variant)",
                "outline":                      "var(--tw-outline)",
                "outline-variant":              "var(--tw-outline-variant)",
                "background":                   "var(--tw-background)",
                "on-background":                "var(--tw-on-background)",
                "inverse-surface":              "var(--tw-inverse-surface)",
                "inverse-on-surface":           "var(--tw-inverse-on-surface)",
                "error":                        "var(--tw-error)",
                "on-error":                     "var(--tw-on-error)",
                "error-container":              "var(--tw-error-container)",
                "on-error-container":           "var(--tw-on-error-container)",
                "surface-tint":                 "var(--tw-surface-tint)",
                "accent-cyan":                  "var(--tw-accent-cyan)"
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
                "display-lg": ["Plus Jakarta Sans", "Inter", "sans-serif"],
                "headline-lg": ["Plus Jakarta Sans", "Inter", "sans-serif"],
                "headline-lg-mobile": ["Plus Jakarta Sans", "Inter", "sans-serif"],
                "title-md": ["Plus Jakarta Sans", "Inter", "sans-serif"],
                "body-md": ["Inter", "sans-serif"],
                "body-sm": ["Inter", "sans-serif"],
                "label-caps": ["Inter", "sans-serif"]
            },
            "fontSize": {
                "display-lg": ["48px", {"lineHeight": "60px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                "headline-lg-mobile": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
                "title-md": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
                "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
                "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                "label-caps": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}]
            },
            "boxShadow": {
                "glow-violet": "0 0 20px rgba(70,72,212,0.18)",
                "glow-cyan": "0 0 20px rgba(96,99,238,0.15)",
                "card": "0 1px 2px rgba(27,27,29,0.04), 0 4px 6px -1px rgba(27,27,29,0.05), 0 0 32px rgba(70,72,212,0.06)",
                "card-hover": "0 8px 32px rgba(70,72,212,0.12), 0 4px 12px rgba(27,27,29,0.06)"
            }
        }
    }
}
