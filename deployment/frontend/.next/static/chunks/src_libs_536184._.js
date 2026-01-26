(globalThis.TURBOPACK = globalThis.TURBOPACK || []).push(["static/chunks/src_libs_536184._.js", {

"[project]/src/libs/ApexCharts.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$shared$2f$lib$2f$app$2d$dynamic$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/shared/lib/app-dynamic.js [app-client] (ecmascript)");
;
const Chart = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$shared$2f$lib$2f$app$2d$dynamic$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(_c = ()=>__turbopack_require__("[project]/node_modules/react-apexcharts/dist/react-apexcharts.min.js [app-client] (ecmascript, async loader)")(__turbopack_import__), {
    loadableGenerated: {
        modules: [
            "src/libs/ApexCharts.tsx -> " + "react-apexcharts"
        ]
    },
    ssr: false
});
_c1 = Chart;
const __TURBOPACK__default__export__ = Chart;
var _c, _c1;
__turbopack_refresh__.register(_c, "Chart$dynamic");
__turbopack_refresh__.register(_c1, "Chart");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/libs/styles/AppReactApexCharts.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
// Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f$libs$2f$ApexCharts$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/libs/ApexCharts.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$mui$2f$material$2f$styles$2f$styled$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__$3c$export__default__as__styled$3e$__ = __turbopack_import__("[project]/node_modules/@mui/material/styles/styled.js [app-client] (ecmascript) <locals> <export default as styled>");
// MUI Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$mui$2f$material$2f$Box$2f$Box$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@mui/material/Box/Box.js [app-client] (ecmascript)");
'use client';
;
;
;
;
// Styled Components
const ApexChartWrapper = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$mui$2f$material$2f$styles$2f$styled$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__$3c$export__default__as__styled$3e$__["styled"])(__TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$mui$2f$material$2f$Box$2f$Box$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(({ theme })=>({
        '& .apexcharts-canvas': {
            "& line[stroke='transparent']": {
                display: 'none'
            },
            '& .apexcharts-tooltip': {
                boxShadow: 'var--mui-customShadows-xs)',
                borderColor: 'var(--mui-palette-divider)',
                background: 'var(--mui-palette-background-paper)',
                ...theme.direction === 'rtl' && {
                    '.apexcharts-tooltip-marker': {
                        marginInlineEnd: 10,
                        marginInlineStart: 0
                    },
                    '.apexcharts-tooltip-text-y-value': {
                        marginInlineStart: 5,
                        marginInlineEnd: 0
                    }
                },
                '& .apexcharts-tooltip-title': {
                    fontWeight: 600,
                    borderColor: 'var(--mui-palette-divider)',
                    background: 'var(--mui-palette-background-paper)'
                },
                '&.apexcharts-theme-light': {
                    color: 'var(--mui-palette-text-primary)'
                },
                '&.apexcharts-theme-dark': {
                    color: 'var(--mui-palette-common-white)'
                },
                '& .apexcharts-tooltip-series-group:first-of-type': {
                    paddingBottom: 0
                },
                '& .bar-chart': {
                    padding: theme.spacing(2, 2.5)
                }
            },
            '& .apexcharts-xaxistooltip': {
                borderColor: 'var(--mui-palette-divider)',
                background: 'var(--mui-palette-grey-50)',
                ...theme.applyStyles('dark', {
                    background: 'var(--mui-palette-customColors-bodyBg)'
                }),
                '&:after': {
                    borderBottomColor: 'var(--mui-palette-grey-50)',
                    ...theme.applyStyles('dark', {
                        borderBottomColor: 'var(--mui-palette-customColors-bodyBg)'
                    })
                },
                '&:before': {
                    borderBottomColor: 'var(--mui-palette-divider)'
                }
            },
            '& .apexcharts-yaxistooltip': {
                borderColor: 'var(--mui-palette-divider)',
                background: 'var(--mui-palette-grey-50)',
                ...theme.applyStyles('dark', {
                    background: 'var(--mui-palette-customColors-bodyBg)'
                }),
                '&:after': {
                    borderBottomColor: 'var(--mui-palette-grey-50)',
                    ...theme.applyStyles('dark', {
                        borderBottomColor: 'var(--mui-palette-customColors-bodyBg)'
                    })
                },
                '&:before': {
                    borderLeftColor: 'var(--mui-palette-divider)'
                }
            },
            '& .apexcharts-xaxistooltip-text, & .apexcharts-yaxistooltip-text': {
                color: 'var(--mui-palette-text-primary)'
            },
            '& .apexcharts-yaxis .apexcharts-yaxis-texts-g .apexcharts-yaxis-label': {
                textAnchor: theme.direction === 'rtl' ? 'start' : undefined
            },
            '& .apexcharts-text, & .apexcharts-tooltip-text, & .apexcharts-datalabel-label, & .apexcharts-datalabel, & .apexcharts-xaxistooltip-text, & .apexcharts-yaxistooltip-text, & .apexcharts-legend-text': {
                fontFamily: `${theme.typography.fontFamily} !important`
            },
            '& .apexcharts-pie-label': {
                filter: 'none'
            },
            '& .apexcharts-marker': {
                boxShadow: 'none'
            }
        }
    }));
_c = ApexChartWrapper;
const AppReactApexCharts = (props)=>{
    // Props
    const { boxProps, ...rest } = props;
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(ApexChartWrapper, {
        ...boxProps,
        children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f$libs$2f$ApexCharts$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
            ...rest
        }, void 0, false, {
            fileName: "[project]/src/libs/styles/AppReactApexCharts.tsx",
            lineNumber: 113,
            columnNumber: 7
        }, this)
    }, void 0, false, {
        fileName: "[project]/src/libs/styles/AppReactApexCharts.tsx",
        lineNumber: 112,
        columnNumber: 5
    }, this);
};
_c1 = AppReactApexCharts;
const __TURBOPACK__default__export__ = AppReactApexCharts;
var _c, _c1;
__turbopack_refresh__.register(_c, "ApexChartWrapper");
__turbopack_refresh__.register(_c1, "AppReactApexCharts");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
}]);

//# sourceMappingURL=src_libs_536184._.js.map