tailwind.config = {
    darkMode: "class",
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
                "inverse-primary":              "var(--tw-inverse-primary)",
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
                "headline-lg": ["Inter", "Plus Jakarta Sans"],
                "body-sm": ["Inter"],
                "title-md": ["Inter", "Plus Jakarta Sans"],
                "display-lg": ["Inter", "Plus Jakarta Sans"],
                "label-caps": ["Inter"],
                "body-md": ["Inter"]
            },
            "fontSize": {
                "headline-lg": ["32px", {"lineHeight": "40px", "letterSpacing": "-0.01em", "fontWeight": "700"}],
                "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
                "title-md": ["20px", {"lineHeight": "28px", "fontWeight": "600"}],
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
