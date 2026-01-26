(globalThis.TURBOPACK = globalThis.TURBOPACK || []).push(["static/chunks/src_@menu_9ff34a._.js", {

"[project]/src/@menu/contexts/horizontalNavContext.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
__turbopack_esm__({
    "HorizontalNavProvider": (()=>HorizontalNavProvider),
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
// React Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
;
var _s = __turbopack_refresh__.signature();
'use client';
;
const HorizontalNavContext = /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["createContext"])({});
const HorizontalNavProvider = ({ children })=>{
    _s();
    // States
    const [isBreakpointReached, setIsBreakpointReached] = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useState"])(false);
    // update isBreakpointReached value
    const updateIsBreakpointReached = (isBreakpointReached)=>{
        setIsBreakpointReached(isBreakpointReached);
    };
    // Hooks
    const HorizontalNavProviderValue = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useMemo"])({
        "HorizontalNavProvider.useMemo[HorizontalNavProviderValue]": ()=>({
                isBreakpointReached,
                updateIsBreakpointReached
            })
    }["HorizontalNavProvider.useMemo[HorizontalNavProviderValue]"], [
        isBreakpointReached
    ]);
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(HorizontalNavContext.Provider, {
        value: HorizontalNavProviderValue,
        children: children
    }, void 0, false, {
        fileName: "[project]/src/@menu/contexts/horizontalNavContext.tsx",
        lineNumber: 34,
        columnNumber: 10
    }, this);
};
_s(HorizontalNavProvider, "2mkdBfsQ8zD//wNUDsLO8C9UwYU=");
_c = HorizontalNavProvider;
const __TURBOPACK__default__export__ = HorizontalNavContext;
var _c;
__turbopack_refresh__.register(_c, "HorizontalNavProvider");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/contexts/verticalNavContext.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
__turbopack_esm__({
    "VerticalNavProvider": (()=>VerticalNavProvider),
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
// React Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
;
var _s = __turbopack_refresh__.signature();
'use client';
;
const VerticalNavContext = /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["createContext"])({});
const VerticalNavProvider = ({ children })=>{
    _s();
    // States
    const [verticalNavState, setVerticalNavState] = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useState"])();
    // Hooks
    const updateVerticalNavState = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useCallback"])({
        "VerticalNavProvider.useCallback[updateVerticalNavState]": (values)=>{
            setVerticalNavState({
                "VerticalNavProvider.useCallback[updateVerticalNavState]": (prevState)=>({
                        ...prevState,
                        ...values,
                        collapsing: values.isCollapsed === true,
                        expanding: values.isCollapsed === false
                    })
            }["VerticalNavProvider.useCallback[updateVerticalNavState]"]);
        }
    }["VerticalNavProvider.useCallback[updateVerticalNavState]"], []);
    const collapseVerticalNav = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useCallback"])({
        "VerticalNavProvider.useCallback[collapseVerticalNav]": (value)=>{
            setVerticalNavState({
                "VerticalNavProvider.useCallback[collapseVerticalNav]": (prevState)=>({
                        ...prevState,
                        isHovered: value !== undefined && false,
                        isCollapsed: value !== undefined ? Boolean(value) : !Boolean(prevState?.isCollapsed),
                        collapsing: value === true,
                        expanding: value !== true
                    })
            }["VerticalNavProvider.useCallback[collapseVerticalNav]"]);
        }
    }["VerticalNavProvider.useCallback[collapseVerticalNav]"], []);
    const hoverVerticalNav = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useCallback"])({
        "VerticalNavProvider.useCallback[hoverVerticalNav]": (value)=>{
            setVerticalNavState({
                "VerticalNavProvider.useCallback[hoverVerticalNav]": (prevState)=>({
                        ...prevState,
                        isHovered: value !== undefined ? Boolean(value) : !Boolean(prevState?.isHovered)
                    })
            }["VerticalNavProvider.useCallback[hoverVerticalNav]"]);
        }
    }["VerticalNavProvider.useCallback[hoverVerticalNav]"], []);
    const toggleVerticalNav = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useCallback"])({
        "VerticalNavProvider.useCallback[toggleVerticalNav]": (value)=>{
            setVerticalNavState({
                "VerticalNavProvider.useCallback[toggleVerticalNav]": (prevState)=>({
                        ...prevState,
                        isToggled: value !== undefined ? Boolean(value) : !Boolean(prevState?.isToggled)
                    })
            }["VerticalNavProvider.useCallback[toggleVerticalNav]"]);
        }
    }["VerticalNavProvider.useCallback[toggleVerticalNav]"], []);
    const verticalNavProviderValue = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useMemo"])({
        "VerticalNavProvider.useMemo[verticalNavProviderValue]": ()=>({
                ...verticalNavState,
                updateVerticalNavState,
                collapseVerticalNav,
                hoverVerticalNav,
                toggleVerticalNav
            })
    }["VerticalNavProvider.useMemo[verticalNavProviderValue]"], [
        verticalNavState,
        updateVerticalNavState,
        collapseVerticalNav,
        hoverVerticalNav,
        toggleVerticalNav
    ]);
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(VerticalNavContext.Provider, {
        value: verticalNavProviderValue,
        children: children
    }, void 0, false, {
        fileName: "[project]/src/@menu/contexts/verticalNavContext.tsx",
        lineNumber: 81,
        columnNumber: 10
    }, this);
};
_s(VerticalNavProvider, "uqeXP0d3aqd88VpyBjwK4k8k5Ac=");
_c = VerticalNavProvider;
const __TURBOPACK__default__export__ = VerticalNavContext;
var _c;
__turbopack_refresh__.register(_c, "VerticalNavProvider");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/hooks/useVerticalNav.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// React Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Context Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$contexts$2f$verticalNavContext$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/contexts/verticalNavContext.tsx [app-client] (ecmascript)");
var _s = __turbopack_refresh__.signature();
;
;
const useVerticalNav = ()=>{
    _s();
    // Hooks
    const context = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useContext"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$contexts$2f$verticalNavContext$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"]);
    if (context === undefined) {
        //TODO: set better error message
        throw new Error('VerticalNav Component is required!');
    }
    return context;
};
_s(useVerticalNav, "b9L3QQ+jgeyIrH0NfHrJ8nn7VMU=");
const __TURBOPACK__default__export__ = useVerticalNav;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Common classes for menu components
__turbopack_esm__({
    "horizontalNavClasses": (()=>horizontalNavClasses),
    "menuClasses": (()=>menuClasses),
    "verticalNavClasses": (()=>verticalNavClasses)
});
const menuClasses = {
    root: 'ts-menu-root',
    menuSectionRoot: 'ts-menusection-root',
    menuItemRoot: 'ts-menuitem-root',
    subMenuRoot: 'ts-submenu-root',
    button: 'ts-menu-button',
    prefix: 'ts-menu-prefix',
    suffix: 'ts-menu-suffix',
    label: 'ts-menu-label',
    icon: 'ts-menu-icon',
    menuSectionWrapper: 'ts-menu-section-wrapper',
    menuSectionContent: 'ts-menu-section-content',
    menuSectionLabel: 'ts-menu-section-label',
    subMenuContent: 'ts-submenu-content',
    subMenuExpandIcon: 'ts-submenu-expand-icon',
    disabled: 'ts-disabled',
    active: 'ts-active',
    open: 'ts-open'
};
const verticalNavClasses = {
    root: 'ts-vertical-nav-root',
    container: 'ts-vertical-nav-container',
    bgColorContainer: 'ts-vertical-nav-bg-color-container',
    header: 'ts-vertical-nav-header',
    image: 'ts-vertical-nav-image',
    backdrop: 'ts-vertical-nav-backdrop',
    collapsed: 'ts-collapsed',
    toggled: 'ts-toggled',
    hovered: 'ts-hovered',
    scrollWithContent: 'ts-scroll-with-content',
    breakpointReached: 'ts-breakpoint-reached',
    collapsing: 'ts-collapsing',
    expanding: 'ts-expanding'
};
const horizontalNavClasses = {
    root: 'ts-horizontal-nav-root',
    scrollWithContent: 'ts-scroll-with-content',
    breakpointReached: 'ts-breakpoint-reached'
};
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/vertical/StyledVerticalMenu.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
;
;
const StyledVerticalMenu = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].nav`
  & > ul > :first-of-type {
    margin-block-start: 0;
  }
  &.${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].root} {
    ${({ rootStyles })=>rootStyles}
  }
`;
const __TURBOPACK__default__export__ = StyledVerticalMenu;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/styles.module.css [app-client] (css module)": ((__turbopack_context__) => {

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, t: __turbopack_require_real__ } = __turbopack_context__;
{
__turbopack_export_value__({
  "ul": "styles-module__PwzZdG__ul",
});
}}),
"[project]/src/@menu/defaultConfigs.ts [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Type Imports
__turbopack_esm__({
    "defaultBreakpoints": (()=>defaultBreakpoints),
    "horizontalSubMenuToggleDuration": (()=>horizontalSubMenuToggleDuration),
    "verticalNavToggleDuration": (()=>verticalNavToggleDuration),
    "verticalSubMenuToggleDuration": (()=>verticalSubMenuToggleDuration)
});
const defaultBreakpoints = {
    xs: '480px',
    sm: '600px',
    md: '900px',
    lg: '1200px',
    xl: '1536px',
    xxl: '1920px',
    always: 'always'
};
const verticalNavToggleDuration = 300;
const verticalSubMenuToggleDuration = 300;
const horizontalSubMenuToggleDuration = 200;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/components/vertical-menu/Menu.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
__turbopack_esm__({
    "VerticalMenuContext": (()=>VerticalMenuContext),
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
// React Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Next Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$navigation$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/navigation.js [app-client] (ecmascript)");
// Third-party Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/classnames/index.js [app-client] (ecmascript)");
// Hook Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/hooks/useVerticalNav.tsx [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
// Styled Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$vertical$2f$StyledVerticalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/vertical/StyledVerticalMenu.tsx [app-client] (ecmascript)");
// Style Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$styles$2e$module$2e$css__$5b$app$2d$client$5d$__$28$css__module$29$__ = __turbopack_import__("[project]/src/@menu/styles/styles.module.css [app-client] (css module)");
// Default Config Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$defaultConfigs$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/defaultConfigs.ts [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__ = __turbopack_import__("[project]/node_modules/@floating-ui/react/dist/floating-ui.react.mjs [app-client] (ecmascript) <locals>");
;
var _s = __turbopack_refresh__.signature();
'use client';
;
;
;
;
;
;
;
;
;
const VerticalMenuContext = /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["createContext"])({});
const Menu = (props, ref)=>{
    _s();
    // Props
    const { children, className, rootStyles, menuItemStyles, renderExpandIcon, renderExpandedMenuItemIcon, menuSectionStyles, browserScroll = false, triggerPopout = 'hover', popoutWhenCollapsed = false, subMenuOpenBehavior = 'accordion', transitionDuration = __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$defaultConfigs$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalSubMenuToggleDuration"], collapsedMenuSectionLabel = '-', popoutMenuOffset = {
        mainAxis: 0
    }, textTruncate = true, ...rest } = props;
    // States
    const [openSubmenu, setOpenSubmenu] = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useState"])([]);
    // Refs
    const openSubmenusRef = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useRef"])([]);
    // Hooks
    const pathname = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$navigation$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["usePathname"])();
    const { updateVerticalNavState } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])();
    const toggleOpenSubmenu = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useCallback"])({
        "Menu.useCallback[toggleOpenSubmenu]": (...submenus)=>{
            if (!submenus.length) return;
            const openSubmenuCopy = [
                ...openSubmenu
            ];
            submenus.forEach({
                "Menu.useCallback[toggleOpenSubmenu]": ({ level, label, active = false, id })=>{
                    const submenuIndex = openSubmenuCopy.findIndex({
                        "Menu.useCallback[toggleOpenSubmenu].submenuIndex": (submenu)=>submenu.id === id
                    }["Menu.useCallback[toggleOpenSubmenu].submenuIndex"]);
                    const submenuExists = submenuIndex >= 0;
                    const isAccordion = subMenuOpenBehavior === 'accordion';
                    const inactiveSubmenuIndex = openSubmenuCopy.findIndex({
                        "Menu.useCallback[toggleOpenSubmenu].inactiveSubmenuIndex": (submenu)=>!submenu.active && submenu.level === 0
                    }["Menu.useCallback[toggleOpenSubmenu].inactiveSubmenuIndex"]);
                    // Delete submenu if it exists
                    if (submenuExists) {
                        openSubmenuCopy.splice(submenuIndex, 1);
                    }
                    if (isAccordion) {
                        // Add submenu if it doesn't exist
                        if (!submenuExists) {
                            if (inactiveSubmenuIndex >= 0 && !active && level === 0) {
                                openSubmenuCopy.splice(inactiveSubmenuIndex, 1, {
                                    level,
                                    label,
                                    active,
                                    id
                                });
                            } else {
                                openSubmenuCopy.push({
                                    level,
                                    label,
                                    active,
                                    id
                                });
                            }
                        }
                    } else {
                        // Add submenu if it doesn't exist
                        if (!submenuExists) {
                            openSubmenuCopy.push({
                                level,
                                label,
                                active,
                                id
                            });
                        }
                    }
                }
            }["Menu.useCallback[toggleOpenSubmenu]"]);
            setOpenSubmenu(openSubmenuCopy);
        }
    }["Menu.useCallback[toggleOpenSubmenu]"], [
        openSubmenu,
        subMenuOpenBehavior
    ]);
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "Menu.useEffect": ()=>{
            setOpenSubmenu([
                ...openSubmenusRef.current
            ]);
            openSubmenusRef.current = [];
        }
    }["Menu.useEffect"], [
        pathname
    ]);
    // UseEffect, update verticalNav state to set initial values and update values on change
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "Menu.useEffect": ()=>{
            updateVerticalNavState({
                isPopoutWhenCollapsed: popoutWhenCollapsed
            });
        }
    }["Menu.useEffect"], [
        popoutWhenCollapsed,
        updateVerticalNavState
    ]);
    const providerValue = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useMemo"])({
        "Menu.useMemo[providerValue]": ()=>({
                browserScroll,
                triggerPopout,
                transitionDuration,
                menuItemStyles,
                menuSectionStyles,
                renderExpandIcon,
                renderExpandedMenuItemIcon,
                openSubmenu,
                openSubmenusRef,
                toggleOpenSubmenu,
                subMenuOpenBehavior,
                collapsedMenuSectionLabel,
                popoutMenuOffset,
                textTruncate
            })
    }["Menu.useMemo[providerValue]"], [
        browserScroll,
        triggerPopout,
        transitionDuration,
        menuItemStyles,
        menuSectionStyles,
        renderExpandIcon,
        renderExpandedMenuItemIcon,
        openSubmenu,
        openSubmenusRef,
        toggleOpenSubmenu,
        subMenuOpenBehavior,
        collapsedMenuSectionLabel,
        popoutMenuOffset,
        textTruncate
    ]);
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(VerticalMenuContext.Provider, {
        value: providerValue,
        children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["FloatingTree"], {
            children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$vertical$2f$StyledVerticalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                ref: ref,
                className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].root, className),
                rootStyles: rootStyles,
                ...rest,
                children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("ul", {
                    className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$styles$2e$module$2e$css__$5b$app$2d$client$5d$__$28$css__module$29$__["default"].ul,
                    children: children
                }, void 0, false, {
                    fileName: "[project]/src/@menu/components/vertical-menu/Menu.tsx",
                    lineNumber: 222,
                    columnNumber: 11
                }, this)
            }, void 0, false, {
                fileName: "[project]/src/@menu/components/vertical-menu/Menu.tsx",
                lineNumber: 216,
                columnNumber: 9
            }, this)
        }, void 0, false, {
            fileName: "[project]/src/@menu/components/vertical-menu/Menu.tsx",
            lineNumber: 215,
            columnNumber: 7
        }, this)
    }, void 0, false, {
        fileName: "[project]/src/@menu/components/vertical-menu/Menu.tsx",
        lineNumber: 214,
        columnNumber: 5
    }, this);
};
_s(Menu, "4/o23nGVDzfGqSe6x/kJJw5FIe8=", false, function() {
    return [
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$navigation$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["usePathname"],
        __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"]
    ];
});
_c = Menu;
const __TURBOPACK__default__export__ = /*#__PURE__*/ _c1 = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["forwardRef"])(Menu);
var _c, _c1;
__turbopack_refresh__.register(_c, "Menu");
__turbopack_refresh__.register(_c1, "%default%");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/StyledSubMenuContent.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
;
const StyledSubMenuContent = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].div`
  display: none;
  overflow: hidden;
  z-index: 999;
  transition: ${({ transitionDuration })=>`block-size ${transitionDuration}ms ease-in-out`};
  box-sizing: border-box;

  ${({ isCollapsed, level, isPopoutWhenCollapsed, isHovered })=>isCollapsed && level === 0 && !isPopoutWhenCollapsed && !isHovered && `
      block-size: 0 !important;
    `}

  ${({ isCollapsed, level, isPopoutWhenCollapsed })=>isCollapsed && level === 0 && isPopoutWhenCollapsed ? `
      display: block;
      padding-inline-start: 0px;
      inline-size: 260px;
      border-radius: 4px;
      block-size: auto !important;
      transition: none !important;
      background-color: white;
      box-shadow: 0 3px 6px -4px #0000001f, 0 6px 16px #00000014, 0 9px 28px 8px #0000000d;
     ` : `
      position: static !important;
      transform: none !important;
      `}

  ${({ browserScroll })=>browserScroll && `overflow-y: auto; max-block-size: calc((var(--vh, 1vh) * 100));`}


  ${({ rootStyles })=>rootStyles};
`;
const __TURBOPACK__default__export__ = StyledSubMenuContent;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/components/vertical-menu/SubMenuContent.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// React Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Third-party Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$react$2d$perfect$2d$scrollbar$2f$lib$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/react-perfect-scrollbar/lib/index.js [app-client] (ecmascript)");
// Styled Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledSubMenuContent$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledSubMenuContent.tsx [app-client] (ecmascript)");
// Style Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$styles$2e$module$2e$css__$5b$app$2d$client$5d$__$28$css__module$29$__ = __turbopack_import__("[project]/src/@menu/styles/styles.module.css [app-client] (css module)");
;
var _s = __turbopack_refresh__.signature();
;
;
;
;
const SubMenuContent = (props, ref)=>{
    _s();
    // Props
    const { children, open, level, isCollapsed, isHovered, transitionDuration, isPopoutWhenCollapsed, openWhenCollapsed, browserScroll, ...rest } = props;
    // States
    const [mounted, setMounted] = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useState"])(false);
    // Refs
    const SubMenuContentRef = ref;
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "SubMenuContent.useEffect": ()=>{
            if (mounted) {
                if (open || open && isHovered) {
                    const target = SubMenuContentRef?.current;
                    if (target) {
                        target.style.display = 'block';
                        target.style.overflow = 'hidden';
                        target.style.blockSize = 'auto';
                        const height = target.offsetHeight;
                        target.style.blockSize = '0px';
                        target.offsetHeight;
                        target.style.blockSize = `${height}px`;
                        setTimeout({
                            "SubMenuContent.useEffect": ()=>{
                                target.style.overflow = 'auto';
                                target.style.blockSize = 'auto';
                            }
                        }["SubMenuContent.useEffect"], transitionDuration);
                    }
                } else {
                    const target = SubMenuContentRef?.current;
                    if (target) {
                        target.style.overflow = 'hidden';
                        target.style.blockSize = `${target.offsetHeight}px`;
                        target.offsetHeight;
                        target.style.blockSize = '0px';
                        setTimeout({
                            "SubMenuContent.useEffect": ()=>{
                                target.style.overflow = 'auto';
                                target.style.display = 'none';
                            }
                        }["SubMenuContent.useEffect"], transitionDuration);
                    }
                }
            }
        // eslint-disable-next-line react-hooks/exhaustive-deps
        }
    }["SubMenuContent.useEffect"], [
        open,
        mounted,
        SubMenuContentRef
    ]);
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "SubMenuContent.useEffect": ()=>{
            setMounted(true);
        }
    }["SubMenuContent.useEffect"], [
        isHovered
    ]);
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledSubMenuContent$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
        ref: ref,
        level: level,
        isCollapsed: isCollapsed,
        isHovered: isHovered,
        open: open,
        openWhenCollapsed: openWhenCollapsed,
        isPopoutWhenCollapsed: isPopoutWhenCollapsed,
        transitionDuration: transitionDuration,
        browserScroll: browserScroll,
        ...rest,
        children: !browserScroll && level === 0 && isPopoutWhenCollapsed && isCollapsed ? /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$react$2d$perfect$2d$scrollbar$2f$lib$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
            options: {
                wheelPropagation: false,
                suppressScrollX: true
            },
            style: {
                maxBlockSize: `calc((var(--vh, 1vh) * 100))`
            },
            children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("ul", {
                className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$styles$2e$module$2e$css__$5b$app$2d$client$5d$__$28$css__module$29$__["default"].ul,
                children: children
            }, void 0, false, {
                fileName: "[project]/src/@menu/components/vertical-menu/SubMenuContent.tsx",
                lineNumber: 116,
                columnNumber: 11
            }, this)
        }, void 0, false, {
            fileName: "[project]/src/@menu/components/vertical-menu/SubMenuContent.tsx",
            lineNumber: 112,
            columnNumber: 9
        }, this) : /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("ul", {
            className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$styles$2e$module$2e$css__$5b$app$2d$client$5d$__$28$css__module$29$__["default"].ul,
            children: children
        }, void 0, false, {
            fileName: "[project]/src/@menu/components/vertical-menu/SubMenuContent.tsx",
            lineNumber: 119,
            columnNumber: 9
        }, this)
    }, void 0, false, {
        fileName: "[project]/src/@menu/components/vertical-menu/SubMenuContent.tsx",
        lineNumber: 98,
        columnNumber: 5
    }, this);
};
_s(SubMenuContent, "BShlRgxf1Xjno/mi6QXyq9ZqIDE=");
_c = SubMenuContent;
const __TURBOPACK__default__export__ = /*#__PURE__*/ _c1 = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["forwardRef"])(SubMenuContent);
var _c, _c1;
__turbopack_refresh__.register(_c, "SubMenuContent");
__turbopack_refresh__.register(_c1, "%default%");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/components/RouterLink.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
__turbopack_esm__({
    "RouterLink": (()=>RouterLink)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
// React Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Next Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$client$2f$app$2d$dir$2f$link$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/client/app-dir/link.js [app-client] (ecmascript)");
'use client';
;
;
;
const RouterLink = /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["forwardRef"])(_c = (props, ref)=>{
    // Props
    const { href, className, ...other } = props;
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$client$2f$app$2d$dir$2f$link$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
        ref: ref,
        href: href,
        className: className,
        ...other,
        children: props.children
    }, void 0, false, {
        fileName: "[project]/src/@menu/components/RouterLink.tsx",
        lineNumber: 23,
        columnNumber: 5
    }, this);
});
_c1 = RouterLink;
var _c, _c1;
__turbopack_refresh__.register(_c, "RouterLink$forwardRef");
__turbopack_refresh__.register(_c1, "RouterLink");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/components/vertical-menu/MenuButton.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// React Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__),
    "menuButtonStyles": (()=>menuButtonStyles)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Third-party Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/classnames/index.js [app-client] (ecmascript)");
// Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$RouterLink$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/RouterLink.tsx [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$react$2f$dist$2f$emotion$2d$react$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__ = __turbopack_import__("[project]/node_modules/@emotion/react/dist/emotion-react.browser.development.esm.js [app-client] (ecmascript) <locals>");
;
;
;
;
;
;
const menuButtonStyles = (props)=>{
    // Props
    const { level, disabled, children, isCollapsed, isPopoutWhenCollapsed } = props;
    return (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$react$2f$dist$2f$emotion$2d$react$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["css"])({
        display: 'flex',
        alignItems: 'center',
        minBlockSize: '30px',
        textDecoration: 'none',
        color: 'inherit',
        boxSizing: 'border-box',
        cursor: 'pointer',
        paddingInlineEnd: '20px',
        paddingInlineStart: `${level === 0 ? 20 : (isPopoutWhenCollapsed && isCollapsed ? level : level + 1) * 20}px`,
        '&:hover, &[aria-expanded="true"]': {
            backgroundColor: '#f3f3f3'
        },
        '&:focus-visible': {
            outline: 'none',
            backgroundColor: '#f3f3f3'
        },
        ...disabled && {
            pointerEvents: 'none',
            cursor: 'default',
            color: '#adadad'
        },
        // All the active styles are applied to the button including menu items or submenu
        [`&.${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].active}`]: {
            ...!children && {
                color: 'white'
            },
            backgroundColor: children ? '#f3f3f3' : '#765feb'
        }
    });
};
const MenuButton = ({ className, component, children, ...rest }, ref)=>{
    if (component) {
        // If component is a string, create a new element of that type
        if (typeof component === 'string') {
            return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["createElement"])(component, {
                className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(className),
                ...rest,
                ref
            }, children);
        } else {
            // Otherwise, clone the element
            const { className: classNameProp, ...props } = component.props;
            return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["cloneElement"])(component, {
                className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(className, classNameProp),
                ...rest,
                ...props,
                ref
            }, children);
        }
    } else {
        // If there is no component but href is defined, render RouterLink
        if (rest.href) {
            return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$RouterLink$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["RouterLink"], {
                ref: ref,
                className: className,
                href: rest.href,
                ...rest,
                children: children
            }, void 0, false, {
                fileName: "[project]/src/@menu/components/vertical-menu/MenuButton.tsx",
                lineNumber: 99,
                columnNumber: 9
            }, this);
        } else {
            return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("a", {
                ref: ref,
                className: className,
                ...rest,
                children: children
            }, void 0, false, {
                fileName: "[project]/src/@menu/components/vertical-menu/MenuButton.tsx",
                lineNumber: 105,
                columnNumber: 9
            }, this);
        }
    }
};
_c = MenuButton;
const __TURBOPACK__default__export__ = /*#__PURE__*/ _c1 = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["forwardRef"])(MenuButton);
var _c, _c1;
__turbopack_refresh__.register(_c, "MenuButton");
__turbopack_refresh__.register(_c1, "%default%");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/svg/ChevronRight.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// React Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
;
const ChevronRight = (props)=>{
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("svg", {
        xmlns: "http://www.w3.org/2000/svg",
        width: "1em",
        height: "1em",
        fontSize: "1.5rem",
        viewBox: "0 0 24 24",
        ...props,
        children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("path", {
            fill: "currentColor",
            d: "M10.707 17.707 16.414 12l-5.707-5.707-1.414 1.414L13.586 12l-4.293 4.293z"
        }, void 0, false, {
            fileName: "[project]/src/@menu/svg/ChevronRight.tsx",
            lineNumber: 7,
            columnNumber: 7
        }, this)
    }, void 0, false, {
        fileName: "[project]/src/@menu/svg/ChevronRight.tsx",
        lineNumber: 6,
        columnNumber: 5
    }, this);
};
_c = ChevronRight;
const __TURBOPACK__default__export__ = ChevronRight;
var _c;
__turbopack_refresh__.register(_c, "ChevronRight");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/hooks/useVerticalMenu.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// React Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Context Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$Menu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/Menu.tsx [app-client] (ecmascript)");
var _s = __turbopack_refresh__.signature();
;
;
const useVerticalMenu = ()=>{
    _s();
    // Hooks
    const context = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useContext"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$Menu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["VerticalMenuContext"]);
    if (context === undefined) {
        //TODO: set better error message
        throw new Error('Menu Component is required!');
    }
    return context;
};
_s(useVerticalMenu, "b9L3QQ+jgeyIrH0NfHrJ8nn7VMU=");
const __TURBOPACK__default__export__ = useVerticalMenu;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/horizontal/StyledHorizontalMenu.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
;
;
const StyledHorizontalMenu = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].nav`
  &.${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].root} {
    ${({ rootStyles })=>rootStyles}
  }
`;
const __TURBOPACK__default__export__ = StyledHorizontalMenu;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/horizontal/horizontalUl.module.css [app-client] (css module)": ((__turbopack_context__) => {

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, t: __turbopack_require_real__ } = __turbopack_context__;
{
__turbopack_export_value__({
  "li": "horizontalUl-module__rfHmma__li",
  "root": "horizontalUl-module__rfHmma__root",
});
}}),
"[project]/src/@menu/components/horizontal-menu/Menu.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
__turbopack_esm__({
    "HorizontalMenuContext": (()=>HorizontalMenuContext),
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
// React Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Third-party Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/classnames/index.js [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
// Styled Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$StyledHorizontalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/horizontal/StyledHorizontalMenu.tsx [app-client] (ecmascript)");
// Style Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$horizontalUl$2e$module$2e$css__$5b$app$2d$client$5d$__$28$css__module$29$__ = __turbopack_import__("[project]/src/@menu/styles/horizontal/horizontalUl.module.css [app-client] (css module)");
// Default Config Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$defaultConfigs$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/defaultConfigs.ts [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__ = __turbopack_import__("[project]/node_modules/@floating-ui/react/dist/floating-ui.react.mjs [app-client] (ecmascript) <locals>");
;
var _s = __turbopack_refresh__.signature();
'use client';
;
;
;
;
;
;
;
const HorizontalMenuContext = /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["createContext"])({});
const Menu = (props, ref)=>{
    _s();
    // Props
    const { children, className, rootStyles, menuItemStyles, triggerPopout = 'hover', browserScroll = false, transitionDuration = __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$defaultConfigs$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["horizontalSubMenuToggleDuration"], renderExpandIcon, renderExpandedMenuItemIcon, popoutMenuOffset = {
        mainAxis: 0
    }, textTruncate = true, verticalMenuProps, ...rest } = props;
    const providerValue = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useMemo"])({
        "Menu.useMemo[providerValue]": ()=>({
                triggerPopout,
                browserScroll,
                menuItemStyles,
                renderExpandIcon,
                renderExpandedMenuItemIcon,
                transitionDuration,
                popoutMenuOffset,
                textTruncate,
                verticalMenuProps
            })
    }["Menu.useMemo[providerValue]"], [
        triggerPopout,
        browserScroll,
        menuItemStyles,
        renderExpandIcon,
        renderExpandedMenuItemIcon,
        transitionDuration,
        popoutMenuOffset,
        textTruncate,
        verticalMenuProps
    ]);
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(HorizontalMenuContext.Provider, {
        value: providerValue,
        children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["FloatingTree"], {
            children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$StyledHorizontalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                ref: ref,
                className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].root, className),
                rootStyles: rootStyles,
                ...rest,
                children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("ul", {
                    className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$horizontalUl$2e$module$2e$css__$5b$app$2d$client$5d$__$28$css__module$29$__["default"].root,
                    children: children
                }, void 0, false, {
                    fileName: "[project]/src/@menu/components/horizontal-menu/Menu.tsx",
                    lineNumber: 117,
                    columnNumber: 11
                }, this)
            }, void 0, false, {
                fileName: "[project]/src/@menu/components/horizontal-menu/Menu.tsx",
                lineNumber: 111,
                columnNumber: 9
            }, this)
        }, void 0, false, {
            fileName: "[project]/src/@menu/components/horizontal-menu/Menu.tsx",
            lineNumber: 110,
            columnNumber: 7
        }, this)
    }, void 0, false, {
        fileName: "[project]/src/@menu/components/horizontal-menu/Menu.tsx",
        lineNumber: 109,
        columnNumber: 5
    }, this);
};
_s(Menu, "4Yynjx1hqx+IWzMZIMxn+aTwOw8=");
_c = Menu;
const __TURBOPACK__default__export__ = /*#__PURE__*/ _c1 = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["forwardRef"])(Menu);
var _c, _c1;
__turbopack_refresh__.register(_c, "Menu");
__turbopack_refresh__.register(_c1, "%default%");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/horizontal/StyledHorizontalSubMenuContent.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
;
const StyledHorizontalSubMenuContent = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].div`
  inline-size: 260px;
  border-radius: 4px;
  box-shadow: 0 9px 28px 8px #00000011;
  outline: none;
  box-sizing: border-box;
  background-color: white;
  overflow: hidden;

  ${({ browserScroll, top })=>browserScroll && `overflow-y: auto; max-block-size: calc((var(--vh, 1vh) * 100) - ${top}px);`}
  ${({ rootStyles })=>rootStyles};
`;
const __TURBOPACK__default__export__ = StyledHorizontalSubMenuContent;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/components/horizontal-menu/SubMenuContent.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// React Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Third-party Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$react$2d$perfect$2d$scrollbar$2f$lib$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/react-perfect-scrollbar/lib/index.js [app-client] (ecmascript)");
// Styled Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$StyledHorizontalSubMenuContent$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/horizontal/StyledHorizontalSubMenuContent.tsx [app-client] (ecmascript)");
// Style Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$styles$2e$module$2e$css__$5b$app$2d$client$5d$__$28$css__module$29$__ = __turbopack_import__("[project]/src/@menu/styles/styles.module.css [app-client] (css module)");
;
;
;
;
;
const SubMenuContent = (props, ref)=>{
    // Props
    const { children, open, firstLevel, top, browserScroll, ...rest } = props;
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$StyledHorizontalSubMenuContent$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
        ref: ref,
        firstLevel: firstLevel,
        open: open,
        top: top,
        browserScroll: browserScroll,
        ...rest,
        children: !browserScroll ? /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$react$2d$perfect$2d$scrollbar$2f$lib$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
            options: {
                wheelPropagation: false,
                suppressScrollX: true
            },
            style: {
                maxBlockSize: `calc((var(--vh, 1vh) * 100) - ${top}px)`
            },
            children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("ul", {
                className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$styles$2e$module$2e$css__$5b$app$2d$client$5d$__$28$css__module$29$__["default"].ul,
                children: children
            }, void 0, false, {
                fileName: "[project]/src/@menu/components/horizontal-menu/SubMenuContent.tsx",
                lineNumber: 45,
                columnNumber: 11
            }, this)
        }, void 0, false, {
            fileName: "[project]/src/@menu/components/horizontal-menu/SubMenuContent.tsx",
            lineNumber: 41,
            columnNumber: 9
        }, this) : /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("ul", {
            className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$styles$2e$module$2e$css__$5b$app$2d$client$5d$__$28$css__module$29$__["default"].ul,
            children: children
        }, void 0, false, {
            fileName: "[project]/src/@menu/components/horizontal-menu/SubMenuContent.tsx",
            lineNumber: 48,
            columnNumber: 9
        }, this)
    }, void 0, false, {
        fileName: "[project]/src/@menu/components/horizontal-menu/SubMenuContent.tsx",
        lineNumber: 31,
        columnNumber: 5
    }, this);
};
_c = SubMenuContent;
const __TURBOPACK__default__export__ = /*#__PURE__*/ _c1 = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["forwardRef"])(SubMenuContent);
var _c, _c1;
__turbopack_refresh__.register(_c, "SubMenuContent");
__turbopack_refresh__.register(_c1, "%default%");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/hooks/useHorizontalMenu.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// React Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Context Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$Menu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/Menu.tsx [app-client] (ecmascript)");
var _s = __turbopack_refresh__.signature();
;
;
const useHorizontalMenu = ()=>{
    _s();
    // Hooks
    const context = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useContext"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$Menu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["HorizontalMenuContext"]);
    if (context === undefined) {
        //TODO: set better error message
        throw new Error('Menu Component is required!');
    }
    return context;
};
_s(useHorizontalMenu, "b9L3QQ+jgeyIrH0NfHrJ8nn7VMU=");
const __TURBOPACK__default__export__ = useHorizontalMenu;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/components/horizontal-menu/MenuButton.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// React Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__),
    "menuButtonStyles": (()=>menuButtonStyles)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Third-party Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/classnames/index.js [app-client] (ecmascript)");
// Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$RouterLink$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/RouterLink.tsx [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$react$2f$dist$2f$emotion$2d$react$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__ = __turbopack_import__("[project]/node_modules/@emotion/react/dist/emotion-react.browser.development.esm.js [app-client] (ecmascript) <locals>");
;
;
;
;
;
;
const menuButtonStyles = (props)=>{
    // Props
    const { level, disabled, children } = props;
    return (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$react$2f$dist$2f$emotion$2d$react$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["css"])({
        display: 'flex',
        alignItems: 'center',
        minBlockSize: '30px',
        textDecoration: 'none',
        color: 'inherit',
        boxSizing: 'border-box',
        cursor: 'pointer',
        paddingInline: '20px',
        '&:hover': {
            backgroundColor: '#f3f3f3'
        },
        '&:focus-visible': {
            outline: 'none',
            backgroundColor: '#f3f3f3'
        },
        ...disabled && {
            pointerEvents: 'none',
            cursor: 'default',
            color: '#adadad'
        },
        // All the active styles are applied to the button including menu items or submenu
        [`&.${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].active}`]: {
            ...level === 0 ? {
                color: 'white',
                backgroundColor: '#765feb'
            } : {
                ...children ? {
                    backgroundColor: '#f3f3f3'
                } : {
                    color: '#765feb',
                    backgroundColor: '#765feb1f'
                }
            }
        }
    });
};
const MenuButton = ({ className, component, children, ...rest }, ref)=>{
    if (component) {
        // If component is a string, create a new element of that type
        if (typeof component === 'string') {
            return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["createElement"])(component, {
                className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(className),
                ...rest,
                ref
            }, children);
        } else {
            // Otherwise, clone the element
            const { className: classNameProp, ...props } = component.props;
            return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["cloneElement"])(component, {
                className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(className, classNameProp),
                ...rest,
                ...props,
                ref
            }, children);
        }
    } else {
        // If there is no component but href is defined, render RouterLink
        if (rest.href) {
            return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$RouterLink$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["RouterLink"], {
                ref: ref,
                className: className,
                href: rest.href,
                ...rest,
                children: children
            }, void 0, false, {
                fileName: "[project]/src/@menu/components/horizontal-menu/MenuButton.tsx",
                lineNumber: 101,
                columnNumber: 9
            }, this);
        } else {
            return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("a", {
                ref: ref,
                className: className,
                ...rest,
                children: children
            }, void 0, false, {
                fileName: "[project]/src/@menu/components/horizontal-menu/MenuButton.tsx",
                lineNumber: 107,
                columnNumber: 9
            }, this);
        }
    }
};
_c = MenuButton;
const __TURBOPACK__default__export__ = /*#__PURE__*/ _c1 = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["forwardRef"])(MenuButton);
var _c, _c1;
__turbopack_refresh__.register(_c, "MenuButton");
__turbopack_refresh__.register(_c1, "%default%");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/StyledMenuLabel.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
;
const StyledMenuLabel = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].span`
  flex-grow: 1;
  ${({ textTruncate })=>textTruncate && `
      text-overflow: ellipsis;
      overflow: hidden;
      white-space: nowrap;
    `};
  ${({ rootStyles })=>rootStyles};
`;
const __TURBOPACK__default__export__ = StyledMenuLabel;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/StyledMenuPrefix.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
;
const StyledMenuPrefix = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].span`
  margin-inline-end: 5px;
  display: ${({ firstLevel, isCollapsed, isHovered })=>firstLevel && isCollapsed && !isHovered ? 'none' : 'flex'};
  ${({ rootStyles })=>rootStyles};
`;
const __TURBOPACK__default__export__ = StyledMenuPrefix;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/StyledMenuSuffix.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
;
const StyledMenuSuffix = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].span`
  margin-inline-start: 5px;
  display: ${({ firstLevel, isCollapsed, isHovered })=>firstLevel && isCollapsed && !isHovered ? 'none' : 'flex'};
  ${({ rootStyles })=>rootStyles};
`;
const __TURBOPACK__default__export__ = StyledMenuSuffix;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/horizontal/StyledHorizontalNavExpandIcon.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "StyledHorizontalNavExpandIconWrapper": (()=>StyledHorizontalNavExpandIconWrapper),
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
;
const StyledHorizontalNavExpandIconWrapper = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].span`
  display: flex;
  margin-inline-start: 5px;
  ${({ rootStyles })=>rootStyles};
`;
const StyledHorizontalNavExpandIcon = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].span`
  display: flex;

  ${({ level })=>level === 0 && `
    & > i,
    & > svg {
      transform: rotate(90deg);
    }
  `}

  ${({ level })=>level && level > 0 && `
    [dir='rtl'] & > i,
    [dir='rtl'] & > svg {
      transform: rotate(180deg);
    }
  `}
`;
const __TURBOPACK__default__export__ = StyledHorizontalNavExpandIcon;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/horizontal/StyledHorizontalSubMenuContentWrapper.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
;
const StyledHorizontalSubMenuContentWrapper = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].div`
  z-index: 10;

  ${({ rootStyles })=>rootStyles};
`;
const __TURBOPACK__default__export__ = StyledHorizontalSubMenuContentWrapper;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/components/horizontal-menu/SubMenu.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
__turbopack_esm__({
    "HorizontalSubMenuContext": (()=>HorizontalSubMenuContext),
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
// React Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Next Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$navigation$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/navigation.js [app-client] (ecmascript)");
// Third-party Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/classnames/index.js [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
// Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$SubMenuContent$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/SubMenuContent.tsx [app-client] (ecmascript)");
// Hook Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useHorizontalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/hooks/useHorizontalMenu.tsx [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuUtils$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuUtils.tsx [app-client] (ecmascript)");
// Styled Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$MenuButton$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/MenuButton.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuLabel$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledMenuLabel.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuPrefix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledMenuPrefix.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuSuffix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledMenuSuffix.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$StyledHorizontalNavExpandIcon$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/horizontal/StyledHorizontalNavExpandIcon.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$StyledHorizontalSubMenuContentWrapper$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/horizontal/StyledHorizontalSubMenuContentWrapper.tsx [app-client] (ecmascript)");
// Style Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$horizontalUl$2e$module$2e$css__$5b$app$2d$client$5d$__$28$css__module$29$__ = __turbopack_import__("[project]/src/@menu/styles/horizontal/horizontalUl.module.css [app-client] (css module)");
// Icon Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$svg$2f$ChevronRight$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/svg/ChevronRight.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__ = __turbopack_import__("[project]/node_modules/@floating-ui/react/dist/floating-ui.react.mjs [app-client] (ecmascript) <locals>");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2d$dom$2f$dist$2f$floating$2d$ui$2e$react$2d$dom$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__ = __turbopack_import__("[project]/node_modules/@floating-ui/react-dom/dist/floating-ui.react-dom.mjs [app-client] (ecmascript) <locals>");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$dom$2f$dist$2f$floating$2d$ui$2e$dom$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__ = __turbopack_import__("[project]/node_modules/@floating-ui/dom/dist/floating-ui.dom.mjs [app-client] (ecmascript) <locals>");
;
var _s = __turbopack_refresh__.signature();
'use client';
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
const StyledSubMenu = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].li`
  ${({ level })=>level === 0 && {
        borderRadius: '6px',
        overflow: 'hidden'
    }}

  &.${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].open} > .${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].button} {
    background-color: #f3f3f3;
  }

  ${({ menuItemStyles })=>menuItemStyles};
  ${({ rootStyles })=>rootStyles};

  > .${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].button} {
    ${({ level, disabled, children })=>(0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$MenuButton$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuButtonStyles"])({
        level,
        disabled,
        children
    })};
    ${({ buttonStyles })=>buttonStyles};
  }
`;
_c = StyledSubMenu;
const HorizontalSubMenuContext = /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["createContext"])({
    getItemProps: ()=>({})
});
const SubMenu = (props, ref)=>{
    _s();
    // Props
    const { children, className, contentClassName, label, icon, title, prefix, suffix, level = 0, disabled = false, rootStyles, component, onClick, onKeyUp, onOpenChange, ...rest } = props;
    // States
    const [open, setOpen] = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useState"])(false);
    const [active, setActive] = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useState"])(false);
    // Refs
    const dir = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useRef"])('ltr');
    const listItemsRef = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useRef"])([]);
    // Hooks
    const pathname = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$navigation$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["usePathname"])();
    const tree = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useFloatingTree"])();
    const nodeId = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useFloatingNodeId"])();
    const parentId = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useFloatingParentNodeId"])();
    const { triggerPopout, renderExpandIcon, menuItemStyles, browserScroll, transitionDuration, renderExpandedMenuItemIcon, popoutMenuOffset, textTruncate } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useHorizontalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])();
    // Vars
    // Filter out falsy values from children
    const childNodes = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["Children"].toArray(children).filter(Boolean);
    const mainAxisOffset = popoutMenuOffset && popoutMenuOffset.mainAxis && (typeof popoutMenuOffset.mainAxis === 'function' ? popoutMenuOffset.mainAxis({
        level
    }) : popoutMenuOffset.mainAxis);
    const alignmentAxisOffset = popoutMenuOffset && popoutMenuOffset.alignmentAxis && (typeof popoutMenuOffset.alignmentAxis === 'function' ? popoutMenuOffset.alignmentAxis({
        level
    }) : popoutMenuOffset.alignmentAxis);
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "SubMenu.useEffect": ()=>{
            dir.current = window.getComputedStyle(document.documentElement).getPropertyValue('direction');
        }
    }["SubMenu.useEffect"], []);
    const { y, refs, floatingStyles, context } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useFloating"])({
        open,
        nodeId,
        onOpenChange: setOpen,
        placement: level > 0 ? dir.current !== 'rtl' ? 'right-start' : 'left-start' : 'bottom-start',
        middleware: [
            (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2d$dom$2f$dist$2f$floating$2d$ui$2e$react$2d$dom$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["offset"])({
                mainAxis: mainAxisOffset,
                alignmentAxis: alignmentAxisOffset
            }),
            (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2d$dom$2f$dist$2f$floating$2d$ui$2e$react$2d$dom$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["flip"])({
                crossAxis: false
            }),
            (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2d$dom$2f$dist$2f$floating$2d$ui$2e$react$2d$dom$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["shift"])()
        ],
        whileElementsMounted: __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$dom$2f$dist$2f$floating$2d$ui$2e$dom$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["autoUpdate"]
    });
    // Floating UI Transition Styles
    const { isMounted, styles } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useTransitionStyles"])(context, {
        // Configure both open and close durations:
        duration: transitionDuration,
        initial: {
            opacity: 0,
            transform: 'translateY(10px)'
        },
        open: {
            opacity: 1,
            transform: 'translateY(0px)'
        },
        close: {
            opacity: 0,
            transform: 'translateY(10px)'
        }
    });
    const hover = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useHover"])(context, {
        handleClose: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["safePolygon"])({
            blockPointerEvents: true
        }),
        restMs: 25,
        enabled: triggerPopout === 'hover',
        delay: {
            open: 75
        } // Delay opening submenu by 75ms
    });
    const click = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useClick"])(context, {
        enabled: triggerPopout === 'click',
        toggle: false
    });
    const dismiss = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useDismiss"])(context);
    const role = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useRole"])(context, {
        role: 'menu'
    });
    // Merge all the interactions into prop getters
    const { getReferenceProps, getFloatingProps, getItemProps } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useInteractions"])([
        hover,
        click,
        dismiss,
        role
    ]);
    const handleOnClick = (event)=>{
        onClick?.(event);
        triggerPopout === 'click' && setOpen(!open);
    };
    const handleOnKeyUp = (event)=>{
        onKeyUp?.(event);
        if (event.key === 'Enter') {
            setOpen(!open);
        }
    };
    const getSubMenuItemStyles = (element)=>{
        // If the menuItemStyles prop is provided, get the styles for the specified element.
        if (menuItemStyles) {
            // Define the parameters that are passed to the style functions.
            const params = {
                level,
                disabled,
                active,
                isSubmenu: true,
                open: open
            };
            // Get the style function for the specified element.
            const styleFunction = menuItemStyles[element];
            if (styleFunction) {
                // If the style function is a function, call it and return the result.
                // Otherwise, return the style function itself.
                return typeof styleFunction === 'function' ? styleFunction(params) : styleFunction;
            }
        }
    };
    // Event emitter allows you to communicate across tree components.
    // This effect closes all menus when an item gets clicked anywhere in the tree.
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "SubMenu.useEffect": ()=>{
            const handleTreeClick = {
                "SubMenu.useEffect.handleTreeClick": ()=>{
                    setOpen(false);
                }
            }["SubMenu.useEffect.handleTreeClick"];
            const onSubMenuOpen = {
                "SubMenu.useEffect.onSubMenuOpen": (event)=>{
                    if (event.nodeId !== nodeId && event.parentId === parentId) {
                        setOpen(false);
                    }
                }
            }["SubMenu.useEffect.onSubMenuOpen"];
            tree?.events.on('click', handleTreeClick);
            tree?.events.on('menuopen', onSubMenuOpen);
            return ({
                "SubMenu.useEffect": ()=>{
                    tree?.events.off('click', handleTreeClick);
                    tree?.events.off('menuopen', onSubMenuOpen);
                }
            })["SubMenu.useEffect"];
        }
    }["SubMenu.useEffect"], [
        tree,
        nodeId,
        parentId
    ]);
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "SubMenu.useEffect": ()=>{
            if (open) {
                tree?.events.emit('menuopen', {
                    parentId,
                    nodeId
                });
            }
        }
    }["SubMenu.useEffect"], [
        tree,
        open,
        nodeId,
        parentId
    ]);
    // Change active state when the url changes
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "SubMenu.useEffect": ()=>{
            // Check if the current url matches any of the children urls
            if ((0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuUtils$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["confirmUrlInChildren"])(children, pathname)) {
                setActive(true);
            } else {
                setActive(false);
            }
        // eslint-disable-next-line react-hooks/exhaustive-deps
        }
    }["SubMenu.useEffect"], [
        pathname
    ]);
    // User event handler for open state change
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "SubMenu.useEffect": ()=>{
            onOpenChange?.(open);
        // eslint-disable-next-line react-hooks/exhaustive-deps
        }
    }["SubMenu.useEffect"], [
        open
    ]);
    // Merge the reference ref with the ref passed to the component
    const referenceRef = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useMergeRefs"])([
        refs.setReference,
        ref
    ]);
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["FloatingNode"], {
        id: nodeId,
        children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(StyledSubMenu, {
            ...!disabled && {
                ref: referenceRef,
                ...getReferenceProps()
            },
            className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])({
                [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].subMenuRoot]: level === 0
            }, {
                [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].active]: active
            }, {
                [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].disabled]: disabled
            }, {
                [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].open]: open
            }, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$horizontalUl$2e$module$2e$css__$5b$app$2d$client$5d$__$28$css__module$29$__["default"].li, className),
            menuItemStyles: getSubMenuItemStyles('root'),
            level: level,
            disabled: disabled,
            active: active,
            buttonStyles: getSubMenuItemStyles('button'),
            rootStyles: rootStyles,
            children: [
                /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$MenuButton$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                    title: title,
                    className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].button, {
                        [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].active]: active
                    }),
                    component: component,
                    onClick: handleOnClick,
                    onKeyUp: handleOnKeyUp,
                    ...rest,
                    children: [
                        (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuUtils$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["renderMenuIcon"])({
                            icon,
                            level,
                            active,
                            disabled,
                            renderExpandedMenuItemIcon,
                            styles: getSubMenuItemStyles('icon')
                        }),
                        prefix && /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuPrefix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                            firstLevel: level === 0,
                            className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].prefix,
                            rootStyles: getSubMenuItemStyles('prefix'),
                            children: prefix
                        }, void 0, false, {
                            fileName: "[project]/src/@menu/components/horizontal-menu/SubMenu.tsx",
                            lineNumber: 368,
                            columnNumber: 13
                        }, this),
                        /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuLabel$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                            className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].label,
                            rootStyles: getSubMenuItemStyles('label'),
                            textTruncate: textTruncate,
                            children: label
                        }, void 0, false, {
                            fileName: "[project]/src/@menu/components/horizontal-menu/SubMenu.tsx",
                            lineNumber: 378,
                            columnNumber: 11
                        }, this),
                        suffix && /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuSuffix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                            firstLevel: level === 0,
                            className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].suffix,
                            rootStyles: getSubMenuItemStyles('suffix'),
                            children: suffix
                        }, void 0, false, {
                            fileName: "[project]/src/@menu/components/horizontal-menu/SubMenu.tsx",
                            lineNumber: 388,
                            columnNumber: 13
                        }, this),
                        /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$StyledHorizontalNavExpandIcon$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["StyledHorizontalNavExpandIconWrapper"], {
                            className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].subMenuExpandIcon,
                            rootStyles: getSubMenuItemStyles('subMenuExpandIcon'),
                            children: renderExpandIcon ? renderExpandIcon({
                                level,
                                disabled,
                                active,
                                open: open
                            }) : // eslint-disable-next-line lines-around-comment
                            /* Expanded Arrow Icon */ /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$StyledHorizontalNavExpandIcon$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                                level: level,
                                children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$svg$2f$ChevronRight$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                                    fontSize: "1rem"
                                }, void 0, false, {
                                    fileName: "[project]/src/@menu/components/horizontal-menu/SubMenu.tsx",
                                    lineNumber: 413,
                                    columnNumber: 17
                                }, this)
                            }, void 0, false, {
                                fileName: "[project]/src/@menu/components/horizontal-menu/SubMenu.tsx",
                                lineNumber: 412,
                                columnNumber: 15
                            }, this)
                        }, void 0, false, {
                            fileName: "[project]/src/@menu/components/horizontal-menu/SubMenu.tsx",
                            lineNumber: 398,
                            columnNumber: 11
                        }, this)
                    ]
                }, void 0, true, {
                    fileName: "[project]/src/@menu/components/horizontal-menu/SubMenu.tsx",
                    lineNumber: 348,
                    columnNumber: 9
                }, this),
                /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(HorizontalSubMenuContext.Provider, {
                    value: {
                        getItemProps
                    },
                    children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["FloatingPortal"], {
                        children: isMounted && /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$StyledHorizontalSubMenuContentWrapper$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                            ref: refs.setFloating,
                            ...getFloatingProps(),
                            style: floatingStyles,
                            rootStyles: getSubMenuItemStyles('subMenuStyles'),
                            children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$SubMenuContent$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                                open: open,
                                top: y ? y - window.scrollY : 0,
                                firstLevel: level === 0,
                                browserScroll: browserScroll,
                                className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].subMenuContent, contentClassName),
                                rootStyles: getSubMenuItemStyles('subMenuContent'),
                                style: {
                                    ...styles
                                },
                                children: childNodes.map((node, index)=>/*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["cloneElement"])(node, {
                                        ...getItemProps({
                                            ref (node) {
                                                listItemsRef.current[index] = node;
                                            },
                                            onClick (event) {
                                                if (node.props.children && !Array.isArray(node.props.children)) {
                                                    node.props.onClick?.(event);
                                                    tree?.events.emit('click');
                                                }
                                            }
                                        }),
                                        level: level + 1
                                    }))
                            }, void 0, false, {
                                fileName: "[project]/src/@menu/components/horizontal-menu/SubMenu.tsx",
                                lineNumber: 428,
                                columnNumber: 17
                            }, this)
                        }, void 0, false, {
                            fileName: "[project]/src/@menu/components/horizontal-menu/SubMenu.tsx",
                            lineNumber: 422,
                            columnNumber: 15
                        }, this)
                    }, void 0, false, {
                        fileName: "[project]/src/@menu/components/horizontal-menu/SubMenu.tsx",
                        lineNumber: 420,
                        columnNumber: 11
                    }, this)
                }, void 0, false, {
                    fileName: "[project]/src/@menu/components/horizontal-menu/SubMenu.tsx",
                    lineNumber: 419,
                    columnNumber: 9
                }, this)
            ]
        }, void 0, true, {
            fileName: "[project]/src/@menu/components/horizontal-menu/SubMenu.tsx",
            lineNumber: 330,
            columnNumber: 7
        }, this)
    }, void 0, false, {
        fileName: "[project]/src/@menu/components/horizontal-menu/SubMenu.tsx",
        lineNumber: 328,
        columnNumber: 5
    }, this);
};
_s(SubMenu, "ooCOhl1XvetZcX+eYsAAwRbH//A=", false, function() {
    return [
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$navigation$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["usePathname"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useFloatingTree"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useFloatingNodeId"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useFloatingParentNodeId"],
        __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useHorizontalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useFloating"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useTransitionStyles"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useHover"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useClick"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useDismiss"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useRole"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useInteractions"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useMergeRefs"]
    ];
});
_c1 = SubMenu;
const __TURBOPACK__default__export__ = /*#__PURE__*/ _c2 = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["forwardRef"])(SubMenu);
var _c, _c1, _c2;
__turbopack_refresh__.register(_c, "StyledSubMenu");
__turbopack_refresh__.register(_c1, "SubMenu");
__turbopack_refresh__.register(_c2, "%default%");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/horizontal/StyledHorizontalMenuItem.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
// Style Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$MenuButton$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/MenuButton.tsx [app-client] (ecmascript)");
;
;
;
const StyledHorizontalMenuItem = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].li`
  position: relative;
  ${({ level })=>level === 0 && {
        borderRadius: '6px',
        overflow: 'hidden'
    }}
  ${({ menuItemStyles })=>menuItemStyles};
  ${({ rootStyles })=>rootStyles};

  > .${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].button} {
    ${({ level, disabled })=>(0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$MenuButton$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuButtonStyles"])({
        level,
        disabled
    })};
    ${({ buttonStyles })=>buttonStyles};
  }
`;
const __TURBOPACK__default__export__ = StyledHorizontalMenuItem;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/components/horizontal-menu/MenuItem.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
// React Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Next Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$navigation$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/navigation.js [app-client] (ecmascript)");
// Third-party Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/classnames/index.js [app-client] (ecmascript)");
// Context Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$SubMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/SubMenu.tsx [app-client] (ecmascript)");
// Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$MenuButton$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/MenuButton.tsx [app-client] (ecmascript)");
// Hook Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useHorizontalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/hooks/useHorizontalMenu.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/hooks/useVerticalNav.tsx [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuUtils$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuUtils.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
// Styled Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuLabel$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledMenuLabel.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuPrefix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledMenuPrefix.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuSuffix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledMenuSuffix.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$StyledHorizontalMenuItem$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/horizontal/StyledHorizontalMenuItem.tsx [app-client] (ecmascript)");
// Style Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$horizontalUl$2e$module$2e$css__$5b$app$2d$client$5d$__$28$css__module$29$__ = __turbopack_import__("[project]/src/@menu/styles/horizontal/horizontalUl.module.css [app-client] (css module)");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__ = __turbopack_import__("[project]/node_modules/@floating-ui/react/dist/floating-ui.react.mjs [app-client] (ecmascript) <locals>");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$react$2d$use$2f$esm$2f$useUpdateEffect$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__useUpdateEffect$3e$__ = __turbopack_import__("[project]/node_modules/react-use/esm/useUpdateEffect.js [app-client] (ecmascript) <export default as useUpdateEffect>");
;
var _s = __turbopack_refresh__.signature();
'use client';
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
const MenuItem = (props, ref)=>{
    _s();
    // Props
    const { children, icon, className, prefix, suffix, level = 0, disabled = false, exactMatch = true, activeUrl, component, onActiveChange, rootStyles, ...rest } = props;
    // States
    const [active, setActive] = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useState"])(false);
    // Hooks
    const tree = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useFloatingTree"])();
    const pathname = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$navigation$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["usePathname"])();
    const { toggleVerticalNav, isToggled } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])();
    const { getItemProps } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useContext"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$SubMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["HorizontalSubMenuContext"]);
    const { menuItemStyles, renderExpandedMenuItemIcon, textTruncate } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useHorizontalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])();
    const getMenuItemStyles = (element)=>{
        // If the menuItemStyles prop is provided, get the styles for the specified element.
        if (menuItemStyles) {
            // Define the parameters that are passed to the style functions.
            const params = {
                level,
                disabled,
                active,
                isSubmenu: false
            };
            // Get the style function for the specified element.
            const styleFunction = menuItemStyles[element];
            if (styleFunction) {
                // If the style function is a function, call it and return the result.
                // Otherwise, return the style function itself.
                return typeof styleFunction === 'function' ? styleFunction(params) : styleFunction;
            }
        }
    };
    // Handle the click event.
    const handleClick = ()=>{
        if (isToggled) {
            toggleVerticalNav();
        }
    };
    // Change active state when the url changes
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "MenuItem.useEffect": ()=>{
            const href = rest.href || component && typeof component !== 'string' && component.props.href;
            if (href) {
                // Check if the current url matches any of the children urls
                if (exactMatch ? pathname === href : activeUrl && pathname.includes(activeUrl)) {
                    setActive(true);
                } else {
                    setActive(false);
                }
            }
        // eslint-disable-next-line react-hooks/exhaustive-deps
        }
    }["MenuItem.useEffect"], [
        pathname
    ]);
    // Call the onActiveChange callback when the active state changes.
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$react$2d$use$2f$esm$2f$useUpdateEffect$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__useUpdateEffect$3e$__["useUpdateEffect"])({
        "MenuItem.useUpdateEffect": ()=>{
            onActiveChange?.(active);
        }
    }["MenuItem.useUpdateEffect"], [
        active
    ]);
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$StyledHorizontalMenuItem$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
        ref: ref,
        className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])({
            [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].menuItemRoot]: level === 0
        }, {
            [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].active]: active
        }, {
            [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].disabled]: disabled
        }, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$horizontalUl$2e$module$2e$css__$5b$app$2d$client$5d$__$28$css__module$29$__["default"].li, className),
        level: level,
        disabled: disabled,
        buttonStyles: getMenuItemStyles('button'),
        menuItemStyles: getMenuItemStyles('root'),
        rootStyles: rootStyles,
        children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$MenuButton$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
            className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].button, {
                [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].active]: active
            }),
            component: component,
            tabIndex: disabled ? -1 : 0,
            onClick: handleClick,
            ...getItemProps({
                onClick (event) {
                    props.onClick?.(event);
                    tree?.events.emit('click');
                }
            }),
            ...rest,
            children: [
                (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuUtils$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["renderMenuIcon"])({
                    icon,
                    level,
                    active,
                    disabled,
                    renderExpandedMenuItemIcon,
                    styles: getMenuItemStyles('icon')
                }),
                prefix && /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuPrefix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                    firstLevel: level === 0,
                    className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].prefix,
                    rootStyles: getMenuItemStyles('prefix'),
                    children: prefix
                }, void 0, false, {
                    fileName: "[project]/src/@menu/components/horizontal-menu/MenuItem.tsx",
                    lineNumber: 174,
                    columnNumber: 11
                }, this),
                /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuLabel$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                    className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].label,
                    rootStyles: getMenuItemStyles('label'),
                    textTruncate: textTruncate,
                    children: children
                }, void 0, false, {
                    fileName: "[project]/src/@menu/components/horizontal-menu/MenuItem.tsx",
                    lineNumber: 184,
                    columnNumber: 9
                }, this),
                suffix && /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuSuffix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                    firstLevel: level === 0,
                    className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].suffix,
                    rootStyles: getMenuItemStyles('suffix'),
                    children: suffix
                }, void 0, false, {
                    fileName: "[project]/src/@menu/components/horizontal-menu/MenuItem.tsx",
                    lineNumber: 194,
                    columnNumber: 11
                }, this)
            ]
        }, void 0, true, {
            fileName: "[project]/src/@menu/components/horizontal-menu/MenuItem.tsx",
            lineNumber: 149,
            columnNumber: 7
        }, this)
    }, void 0, false, {
        fileName: "[project]/src/@menu/components/horizontal-menu/MenuItem.tsx",
        lineNumber: 134,
        columnNumber: 5
    }, this);
};
_s(MenuItem, "/YRhYFYCkqIXhTvJClgNvEUqwVg=", false, function() {
    return [
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useFloatingTree"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$navigation$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["usePathname"],
        __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"],
        __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useHorizontalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$react$2d$use$2f$esm$2f$useUpdateEffect$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__useUpdateEffect$3e$__["useUpdateEffect"]
    ];
});
_c = MenuItem;
const __TURBOPACK__default__export__ = /*#__PURE__*/ _c1 = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["forwardRef"])(MenuItem);
var _c, _c1;
__turbopack_refresh__.register(_c, "MenuItem");
__turbopack_refresh__.register(_c1, "%default%");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/hooks/useMediaQuery.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
// React Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
var _s = __turbopack_refresh__.signature();
'use client';
;
const useMediaQuery = (breakpoint)=>{
    _s();
    // States
    const [matches, setMatches] = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useState"])(breakpoint === 'always');
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "useMediaQuery.useEffect": ()=>{
            if (breakpoint && breakpoint !== 'always') {
                const media = window.matchMedia(`(max-width: ${breakpoint})`);
                if (media.matches !== matches) {
                    setMatches(media.matches);
                }
                const listener = {
                    "useMediaQuery.useEffect.listener": ()=>setMatches(media.matches)
                }["useMediaQuery.useEffect.listener"];
                window.addEventListener('resize', listener);
                return ({
                    "useMediaQuery.useEffect": ()=>window.removeEventListener('resize', listener)
                })["useMediaQuery.useEffect"];
            }
        }
    }["useMediaQuery.useEffect"], [
        matches,
        breakpoint
    ]);
    return matches;
};
_s(useMediaQuery, "5RmzjQNZwkv+H4H2mMjeELLGmZ0=");
const __TURBOPACK__default__export__ = useMediaQuery;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/StyledBackdrop.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
;
const StyledBackdrop = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].div`
  position: fixed;
  inset-inline-start: 0;
  inset-block-start: 0;
  inset-inline-end: 0;
  inset-block-end: 0;
  z-index: 1;
  background-color: ${({ backdropColor })=>backdropColor || 'rgba(0, 0, 0, 0.3)'};
  touch-action: none;
`;
const __TURBOPACK__default__export__ = StyledBackdrop;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/vertical/StyledVerticalNav.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
;
;
const StyledVerticalNav = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].aside`
  ${({ scrollWithContent })=>!scrollWithContent && `
    position: sticky;
    inset-block-start: 0;
    block-size: 100dvh;
  `}
  z-index: 9;

  /* Transition */
  transition-property: inline-size, min-inline-size, margin-inline-start, inset-inline-start;
  transition-duration: ${({ transitionDuration })=>`${transitionDuration}ms`};
  transition-timing-function: ease-in-out;

  /* Width & Min Width & Margin */
  inline-size: ${({ width })=>`${width}px`};
  min-inline-size: ${({ width })=>`${width}px`};
  &.${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].collapsed} {
    inline-size: ${({ collapsedWidth })=>`${collapsedWidth}px`};
    min-inline-size: ${({ collapsedWidth })=>`${collapsedWidth}px`};
  }

  &.${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].collapsing}, &.${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].expanding} {
    pointer-events: none;
  }

  /* Collapsed & Toggled */
  &.${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].breakpointReached} {
    position: fixed;
    block-size: 100%;
    inset-block-start: 0;
    inset-inline-start: ${({ width })=>`-${width}px`};
    z-index: 100;
    margin: 0;
    &.${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].collapsed} {
      inset-inline-start: -${({ collapsedWidth })=>`${collapsedWidth}px`};
    }
    &.${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].toggled} {
      inset-inline-start: 0;
    }
  }

  ${({ width, isBreakpointReached })=>!isBreakpointReached && `
    &.${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].toggled} {
      margin-inline-start: -${width}px;
    }
  `}

  &.${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["horizontalNavClasses"].root} .${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].root} > ul {
    flex-direction: column;
    align-items: stretch;
  }

  /* User Styles */
  ${({ customStyles })=>customStyles}
`;
const __TURBOPACK__default__export__ = StyledVerticalNav;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/vertical/StyledVerticalNavContainer.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
;
;
const StyledVerticalNavContainer = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].div`
  position: relative;
  block-size: 100%;
  inline-size: 100%;
  border-inline-end: 1px solid #efefef;
  .${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].hovered} &,
  .${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].expanding} & {
    inline-size: ${({ width })=>`${width}px`};
  }

  /* Transition */
  transition-property: inline-size, inset-inline-start;
  transition-duration: ${({ transitionDuration })=>`${transitionDuration}ms`};
  transition-timing-function: ease-in-out;
`;
const __TURBOPACK__default__export__ = StyledVerticalNavContainer;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/vertical/StyledVerticalNavBgColorContainer.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
;
const StyledVerticalNavBgColorContainer = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].div`
  position: relative;
  block-size: 100%;
  z-index: 3;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
  overflow-x: hidden;
  ${({ backgroundColor })=>backgroundColor && `background-color:${backgroundColor};`}
`;
const __TURBOPACK__default__export__ = StyledVerticalNavBgColorContainer;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/vertical/verticalNavBgImage.module.css [app-client] (css module)": ((__turbopack_context__) => {

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, t: __turbopack_require_real__ } = __turbopack_context__;
{
__turbopack_export_value__({
  "root": "verticalNavBgImage-module__nBlmca__root",
});
}}),
"[project]/src/@menu/components/vertical-menu/VerticalNav.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
// React Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Third-party Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/classnames/index.js [app-client] (ecmascript)");
// Hook Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useMediaQuery$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/hooks/useMediaQuery.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/hooks/useVerticalNav.tsx [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
// Styled Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledBackdrop$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledBackdrop.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$vertical$2f$StyledVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/vertical/StyledVerticalNav.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$vertical$2f$StyledVerticalNavContainer$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/vertical/StyledVerticalNavContainer.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$vertical$2f$StyledVerticalNavBgColorContainer$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/vertical/StyledVerticalNavBgColorContainer.tsx [app-client] (ecmascript)");
// Style Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$vertical$2f$verticalNavBgImage$2e$module$2e$css__$5b$app$2d$client$5d$__$28$css__module$29$__ = __turbopack_import__("[project]/src/@menu/styles/vertical/verticalNavBgImage.module.css [app-client] (css module)");
// Default Config Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$defaultConfigs$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/defaultConfigs.ts [app-client] (ecmascript)");
;
var _s = __turbopack_refresh__.signature();
'use client';
;
;
;
;
;
;
;
;
;
;
;
const VerticalNav = (props)=>{
    _s();
    // Props
    const { width = 260, collapsedWidth = 80, defaultCollapsed = false, backgroundColor = 'white', backgroundImage, breakpoint = 'lg', customBreakpoint, breakpoints, transitionDuration = __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$defaultConfigs$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavToggleDuration"], backdropColor, scrollWithContent = false, className, customStyles, children, ...rest } = props;
    // Vars
    const mergedBreakpoints = {
        ...__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$defaultConfigs$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["defaultBreakpoints"],
        ...breakpoints
    };
    // Refs
    const verticalNavCollapsedRef = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useRef"])(false);
    // Hooks
    const { updateVerticalNavState, isCollapsed: isCollapsedContext, width: widthContext, isBreakpointReached: isBreakpointReachedContext, isToggled: isToggledContext, isHovered: isHoveredContext, collapsing: collapsingContext, expanding: expandingContext, isScrollWithContent: isScrollWithContentContext, transitionDuration: transitionDurationContext, isPopoutWhenCollapsed: isPopoutWhenCollapsedContext } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])();
    // Find the breakpoint from which screen size responsive behavior should enable and if its reached or not
    const breakpointReached = (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useMediaQuery$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(customBreakpoint ?? (breakpoint ? mergedBreakpoints[breakpoint] : breakpoint));
    // UseEffect, update verticalNav state to set initial values and update values on change
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "VerticalNav.useEffect": ()=>{
            updateVerticalNavState({
                width,
                collapsedWidth,
                transitionDuration,
                isScrollWithContent: scrollWithContent,
                isBreakpointReached: breakpointReached
            });
            if (!breakpointReached) {
                updateVerticalNavState({
                    isToggled: false
                });
                verticalNavCollapsedRef.current && updateVerticalNavState({
                    isCollapsed: true
                });
            } else {
                if (isCollapsedContext && !verticalNavCollapsedRef.current) {
                    verticalNavCollapsedRef.current = true;
                }
                isCollapsedContext && updateVerticalNavState({
                    isCollapsed: false
                });
                isHoveredContext && updateVerticalNavState({
                    isHovered: false
                });
            }
        // eslint-disable-next-line react-hooks/exhaustive-deps
        }
    }["VerticalNav.useEffect"], [
        width,
        collapsedWidth,
        scrollWithContent,
        breakpointReached,
        updateVerticalNavState
    ]);
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "VerticalNav.useEffect": ()=>{
            if (defaultCollapsed) {
                updateVerticalNavState({
                    isCollapsed: defaultCollapsed,
                    isToggled: false
                });
            }
        // eslint-disable-next-line react-hooks/exhaustive-deps
        }
    }["VerticalNav.useEffect"], [
        defaultCollapsed
    ]);
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "VerticalNav.useEffect": ()=>{
            setTimeout({
                "VerticalNav.useEffect": ()=>{
                    updateVerticalNavState({
                        expanding: false,
                        collapsing: false
                    });
                }
            }["VerticalNav.useEffect"], transitionDuration);
            if (!isCollapsedContext && !breakpointReached && verticalNavCollapsedRef.current) {
                verticalNavCollapsedRef.current = false;
            }
        // eslint-disable-next-line react-hooks/exhaustive-deps
        }
    }["VerticalNav.useEffect"], [
        isCollapsedContext
    ]);
    // Handle Backdrop(Content Overlay) Click
    const handleBackdropClick = ()=>{
        // Close the verticalNav
        updateVerticalNavState({
            isToggled: false
        });
    };
    // Handle VerticalNav Hover Event
    const handleVerticalNavHover = ()=>{
        /* If verticalNav is collapsed then only hover class should be added to verticalNav
      and hover functionality should work (expand verticalNav width) */ if (isCollapsedContext && !isHoveredContext) {
            updateVerticalNavState({
                isHovered: true
            });
        }
    };
    // Handle VerticalNav Hover Out Event
    const handleVerticalNavHoverOut = ()=>{
        // If verticalNav is collapsed then only remove hover class should contract verticalNav width
        if (isCollapsedContext && isHoveredContext) {
            updateVerticalNavState({
                isHovered: false
            });
        }
    };
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$vertical$2f$StyledVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
        width: defaultCollapsed && !widthContext ? collapsedWidth : width,
        isBreakpointReached: isBreakpointReachedContext,
        collapsedWidth: collapsedWidth,
        collapsing: collapsingContext,
        expanding: expandingContext,
        customStyles: customStyles,
        scrollWithContent: isScrollWithContentContext,
        transitionDuration: transitionDurationContext,
        className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].root, {
            [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].collapsed]: isCollapsedContext,
            [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].toggled]: isToggledContext,
            [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].hovered]: isHoveredContext,
            [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].breakpointReached]: isBreakpointReachedContext,
            [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].scrollWithContent]: isScrollWithContentContext,
            [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].collapsing]: collapsingContext,
            [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].expanding]: expandingContext
        }, className),
        ...rest,
        children: [
            /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$vertical$2f$StyledVerticalNavContainer$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                width: widthContext,
                className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].container,
                transitionDuration: transitionDurationContext,
                // eslint-disable-next-line lines-around-comment
                /* Toggle verticalNav on hover only when isPopoutWhenCollapsedContext(default false) is false */ ...!isPopoutWhenCollapsedContext && isCollapsedContext && !breakpointReached && {
                    onMouseEnter: handleVerticalNavHover,
                    onMouseLeave: handleVerticalNavHoverOut
                },
                children: [
                    /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$vertical$2f$StyledVerticalNavBgColorContainer$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                        className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].bgColorContainer,
                        backgroundColor: backgroundColor,
                        children: children
                    }, void 0, false, {
                        fileName: "[project]/src/@menu/components/vertical-menu/VerticalNav.tsx",
                        lineNumber: 208,
                        columnNumber: 9
                    }, this),
                    backgroundImage && // eslint-disable-next-line lines-around-comment
                    /* VerticalNav Background Image */ /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("img", {
                        className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].image, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$vertical$2f$verticalNavBgImage$2e$module$2e$css__$5b$app$2d$client$5d$__$28$css__module$29$__["default"].root),
                        src: backgroundImage,
                        alt: "verticalNav background"
                    }, void 0, false, {
                        fileName: "[project]/src/@menu/components/vertical-menu/VerticalNav.tsx",
                        lineNumber: 219,
                        columnNumber: 11
                    }, this)
                ]
            }, void 0, true, {
                fileName: "[project]/src/@menu/components/vertical-menu/VerticalNav.tsx",
                lineNumber: 192,
                columnNumber: 7
            }, this),
            isToggledContext && breakpointReached && // eslint-disable-next-line lines-around-comment
            /* VerticalNav Backdrop */ /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledBackdrop$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                role: "button",
                tabIndex: 0,
                "aria-label": "backdrop",
                onClick: handleBackdropClick,
                onKeyPress: handleBackdropClick,
                className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].backdrop,
                backdropColor: backdropColor
            }, void 0, false, {
                fileName: "[project]/src/@menu/components/vertical-menu/VerticalNav.tsx",
                lineNumber: 231,
                columnNumber: 9
            }, this)
        ]
    }, void 0, true, {
        fileName: "[project]/src/@menu/components/vertical-menu/VerticalNav.tsx",
        lineNumber: 167,
        columnNumber: 5
    }, this);
};
_s(VerticalNav, "r0EeuQozj5alwqE5j8/NxP8slUc=", false, function() {
    return [
        __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"],
        __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useMediaQuery$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"]
    ];
});
_c = VerticalNav;
const __TURBOPACK__default__export__ = VerticalNav;
var _c;
__turbopack_refresh__.register(_c, "VerticalNav");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/vertical-menu/index.tsx [app-client] (ecmascript) <locals>": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Import all Vertical Nav components and export them
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$VerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/VerticalNav.tsx [app-client] (ecmascript)");
;
;
;
;
;
;
;
const __TURBOPACK__default__export__ = __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$VerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"];
;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/components/horizontal-menu/VerticalNavInHorizontal.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Type Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
// Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$vertical$2d$menu$2f$index$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$module__evaluation$3e$__ = __turbopack_import__("[project]/src/@menu/vertical-menu/index.tsx [app-client] (ecmascript) <module evaluation>");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$vertical$2d$menu$2f$index$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__ = __turbopack_import__("[project]/src/@menu/vertical-menu/index.tsx [app-client] (ecmascript) <locals>");
;
;
const VerticalNavInHorizontal = (props)=>{
    // Props
    const { children, className, breakpoint, customBreakpoint, verticalNavProps } = props;
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$vertical$2d$menu$2f$index$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["default"], {
        ...verticalNavProps,
        className: className,
        breakpoint: breakpoint,
        customBreakpoint: customBreakpoint,
        children: children
    }, void 0, false, {
        fileName: "[project]/src/@menu/components/horizontal-menu/VerticalNavInHorizontal.tsx",
        lineNumber: 21,
        columnNumber: 5
    }, this);
};
_c = VerticalNavInHorizontal;
const __TURBOPACK__default__export__ = VerticalNavInHorizontal;
var _c;
__turbopack_refresh__.register(_c, "VerticalNavInHorizontal");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/hooks/useHorizontalNav.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// React Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Context Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$contexts$2f$horizontalNavContext$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/contexts/horizontalNavContext.tsx [app-client] (ecmascript)");
var _s = __turbopack_refresh__.signature();
;
;
const useHorizontalNav = ()=>{
    _s();
    // Hooks
    const context = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useContext"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$contexts$2f$horizontalNavContext$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"]);
    if (context === undefined) {
        //TODO: set better error message
        throw new Error('HorizontalNav Component is required!');
    }
    return context;
};
_s(useHorizontalNav, "b9L3QQ+jgeyIrH0NfHrJ8nn7VMU=");
const __TURBOPACK__default__export__ = useHorizontalNav;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/horizontal/StyledHorizontalNav.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
;
const StyledHorizontalNav = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].div`
  inline-size: 100%;
  overflow: hidden;
  position: relative;
  ${({ customStyles })=>customStyles}
`;
const __TURBOPACK__default__export__ = StyledHorizontalNav;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/components/horizontal-menu/HorizontalNav.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
// React Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Third-party Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/classnames/index.js [app-client] (ecmascript)");
// Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$VerticalNavInHorizontal$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/VerticalNavInHorizontal.tsx [app-client] (ecmascript)");
// Hook Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useMediaQuery$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/hooks/useMediaQuery.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useHorizontalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/hooks/useHorizontalNav.tsx [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
// Styled Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$StyledHorizontalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/horizontal/StyledHorizontalNav.tsx [app-client] (ecmascript)");
// Default Config Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$defaultConfigs$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/defaultConfigs.ts [app-client] (ecmascript)");
;
var _s = __turbopack_refresh__.signature();
'use client';
;
;
;
;
;
;
;
;
const HorizontalNav = (props)=>{
    _s();
    // Props
    const { switchToVertical = false, hideMenu = false, breakpoint = 'lg', customBreakpoint, breakpoints, customStyles, className, children, verticalNavProps, verticalNavContent: VerticalNavContent } = props;
    // Vars
    const mergedBreakpoints = {
        ...__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$defaultConfigs$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["defaultBreakpoints"],
        ...breakpoints
    };
    const horizontalMenuClasses = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["horizontalNavClasses"].root, className);
    // Refs
    const prevBreakpoint = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useRef"])(false);
    // Hooks
    const { updateIsBreakpointReached } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useHorizontalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])();
    // Find the breakpoint from which screen size responsive behavior should enable and if its reached or not
    const breakpointReached = (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useMediaQuery$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(customBreakpoint ?? (breakpoint ? mergedBreakpoints[breakpoint] : breakpoint));
    // Set the breakpointReached value in the state
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "HorizontalNav.useEffect": ()=>{
            if (prevBreakpoint.current === breakpointReached) return;
            updateIsBreakpointReached(breakpointReached);
            prevBreakpoint.current = breakpointReached;
        // eslint-disable-next-line react-hooks/exhaustive-deps
        }
    }["HorizontalNav.useEffect"], [
        breakpointReached
    ]);
    // If switchToVertical is true, then render the VerticalNav component if breakpoint is reached
    if (switchToVertical && breakpointReached) {
        return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$VerticalNavInHorizontal$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
            breakpoint: breakpoint,
            className: horizontalMenuClasses,
            customBreakpoint: customBreakpoint,
            verticalNavProps: verticalNavProps,
            children: VerticalNavContent && /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(VerticalNavContent, {
                children: children
            }, void 0, false, {
                fileName: "[project]/src/@menu/components/horizontal-menu/HorizontalNav.tsx",
                lineNumber: 92,
                columnNumber: 32
            }, this)
        }, void 0, false, {
            fileName: "[project]/src/@menu/components/horizontal-menu/HorizontalNav.tsx",
            lineNumber: 86,
            columnNumber: 7
        }, this);
    }
    // If hideMenu is true, then hide the HorizontalNav component if breakpoint is reached
    if (hideMenu && breakpointReached) {
        return null;
    }
    // If switchToVertical & hideMenu are false, then render the HorizontalNav component
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$horizontal$2f$StyledHorizontalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
        customStyles: customStyles,
        className: horizontalMenuClasses,
        children: children
    }, void 0, false, {
        fileName: "[project]/src/@menu/components/horizontal-menu/HorizontalNav.tsx",
        lineNumber: 104,
        columnNumber: 5
    }, this);
};
_s(HorizontalNav, "PdrhvyWYvxDKyqjzajkCQ44hmdw=", false, function() {
    return [
        __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useHorizontalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"],
        __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useMediaQuery$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"]
    ];
});
_c = HorizontalNav;
const __TURBOPACK__default__export__ = HorizontalNav;
var _c;
__turbopack_refresh__.register(_c, "HorizontalNav");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/horizontal-menu/index.tsx [app-client] (ecmascript) <locals>": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Import all Horizontal Nav components and export them
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$HorizontalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/HorizontalNav.tsx [app-client] (ecmascript)");
;
;
;
;
const __TURBOPACK__default__export__ = __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$HorizontalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"];
;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/horizontal-menu/index.tsx [app-client] (ecmascript) <module evaluation>": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, t: __turbopack_require_real__ } = __turbopack_context__;
{
__turbopack_esm__({});
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$Menu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/Menu.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$SubMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/SubMenu.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$MenuItem$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/MenuItem.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$HorizontalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/HorizontalNav.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$horizontal$2d$menu$2f$index$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__ = __turbopack_import__("[project]/src/@menu/horizontal-menu/index.tsx [app-client] (ecmascript) <locals>");
}}),
"[project]/src/@menu/styles/vertical/StyledVerticalMenuItem.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
// Style Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$MenuButton$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/MenuButton.tsx [app-client] (ecmascript)");
;
;
;
const StyledVerticalMenuItem = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].li`
  position: relative;
  margin-block-start: 4px;
  ${({ menuItemStyles })=>menuItemStyles};
  ${({ rootStyles })=>rootStyles};

  > .${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].button} {
    ${({ level, disabled, isCollapsed, isPopoutWhenCollapsed })=>(0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$MenuButton$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuButtonStyles"])({
        level,
        disabled,
        isCollapsed,
        isPopoutWhenCollapsed
    })};
    ${({ buttonStyles })=>buttonStyles};
  }
`;
const __TURBOPACK__default__export__ = StyledVerticalMenuItem;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/components/vertical-menu/MenuItem.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
// React Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Next Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$navigation$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/navigation.js [app-client] (ecmascript)");
// Third-party Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/classnames/index.js [app-client] (ecmascript)");
// Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$MenuButton$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/MenuButton.tsx [app-client] (ecmascript)");
// Hook Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/hooks/useVerticalNav.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/hooks/useVerticalMenu.tsx [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuUtils$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuUtils.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
// Styled Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuLabel$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledMenuLabel.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuPrefix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledMenuPrefix.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuSuffix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledMenuSuffix.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$vertical$2f$StyledVerticalMenuItem$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/vertical/StyledVerticalMenuItem.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$react$2d$use$2f$esm$2f$useUpdateEffect$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__useUpdateEffect$3e$__ = __turbopack_import__("[project]/node_modules/react-use/esm/useUpdateEffect.js [app-client] (ecmascript) <export default as useUpdateEffect>");
;
var _s = __turbopack_refresh__.signature();
'use client';
;
;
;
;
;
;
;
;
;
;
;
;
;
const MenuItem = (props, ref)=>{
    _s();
    // Props
    const { children, icon, className, prefix, suffix, level = 0, disabled = false, exactMatch = true, activeUrl, component, onActiveChange, rootStyles, ...rest } = props;
    // States
    const [active, setActive] = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useState"])(false);
    // Hooks
    const pathname = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$navigation$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["usePathname"])();
    const { menuItemStyles, renderExpandedMenuItemIcon, textTruncate } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])();
    const { isCollapsed, isHovered, isPopoutWhenCollapsed, toggleVerticalNav, isToggled, isBreakpointReached } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])();
    // Get the styles for the specified element.
    const getMenuItemStyles = (element)=>{
        // If the menuItemStyles prop is provided, get the styles for the specified element.
        if (menuItemStyles) {
            // Define the parameters that are passed to the style functions.
            const params = {
                level,
                disabled,
                active,
                isSubmenu: false
            };
            // Get the style function for the specified element.
            const styleFunction = menuItemStyles[element];
            if (styleFunction) {
                // If the style function is a function, call it and return the result.
                // Otherwise, return the style function itself.
                return typeof styleFunction === 'function' ? styleFunction(params) : styleFunction;
            }
        }
    };
    // Handle the click event.
    const handleClick = ()=>{
        if (isToggled) {
            toggleVerticalNav();
        }
    };
    // Change active state when the url changes
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "MenuItem.useEffect": ()=>{
            const href = rest.href || component && typeof component !== 'string' && component.props.href;
            if (href) {
                // Check if the current url matches any of the children urls
                if (exactMatch ? pathname === href : activeUrl && pathname.includes(activeUrl)) {
                    setActive(true);
                } else {
                    setActive(false);
                }
            }
        // eslint-disable-next-line react-hooks/exhaustive-deps
        }
    }["MenuItem.useEffect"], [
        pathname
    ]);
    // Call the onActiveChange callback when the active state changes.
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$react$2d$use$2f$esm$2f$useUpdateEffect$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__useUpdateEffect$3e$__["useUpdateEffect"])({
        "MenuItem.useUpdateEffect": ()=>{
            onActiveChange?.(active);
        }
    }["MenuItem.useUpdateEffect"], [
        active
    ]);
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$vertical$2f$StyledVerticalMenuItem$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
        ref: ref,
        className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].menuItemRoot, {
            [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].disabled]: disabled
        }, {
            [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].active]: active
        }, className),
        level: level,
        isCollapsed: isCollapsed,
        isPopoutWhenCollapsed: isPopoutWhenCollapsed,
        disabled: disabled,
        buttonStyles: getMenuItemStyles('button'),
        menuItemStyles: getMenuItemStyles('root'),
        rootStyles: rootStyles,
        children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$MenuButton$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
            className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].button, {
                [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].active]: active
            }),
            component: component,
            tabIndex: disabled ? -1 : 0,
            ...rest,
            onClick: (e)=>{
                handleClick();
                rest.onClick && rest.onClick(e);
            },
            children: [
                (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuUtils$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["renderMenuIcon"])({
                    icon,
                    level,
                    active,
                    disabled,
                    renderExpandedMenuItemIcon,
                    styles: getMenuItemStyles('icon'),
                    isBreakpointReached
                }),
                prefix && /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuPrefix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                    isHovered: isHovered,
                    isCollapsed: isCollapsed,
                    firstLevel: level === 0,
                    className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].prefix,
                    rootStyles: getMenuItemStyles('prefix'),
                    children: prefix
                }, void 0, false, {
                    fileName: "[project]/src/@menu/components/vertical-menu/MenuItem.tsx",
                    lineNumber: 167,
                    columnNumber: 11
                }, this),
                /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuLabel$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                    className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].label,
                    rootStyles: getMenuItemStyles('label'),
                    textTruncate: textTruncate,
                    children: children
                }, void 0, false, {
                    fileName: "[project]/src/@menu/components/vertical-menu/MenuItem.tsx",
                    lineNumber: 179,
                    columnNumber: 9
                }, this),
                suffix && /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuSuffix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                    isHovered: isHovered,
                    isCollapsed: isCollapsed,
                    firstLevel: level === 0,
                    className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].suffix,
                    rootStyles: getMenuItemStyles('suffix'),
                    children: suffix
                }, void 0, false, {
                    fileName: "[project]/src/@menu/components/vertical-menu/MenuItem.tsx",
                    lineNumber: 189,
                    columnNumber: 11
                }, this)
            ]
        }, void 0, true, {
            fileName: "[project]/src/@menu/components/vertical-menu/MenuItem.tsx",
            lineNumber: 144,
            columnNumber: 7
        }, this)
    }, void 0, false, {
        fileName: "[project]/src/@menu/components/vertical-menu/MenuItem.tsx",
        lineNumber: 128,
        columnNumber: 5
    }, this);
};
_s(MenuItem, "BG/Uvvc3NNDKzKyOM87yigprCjk=", false, function() {
    return [
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$navigation$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["usePathname"],
        __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"],
        __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$react$2d$use$2f$esm$2f$useUpdateEffect$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__useUpdateEffect$3e$__["useUpdateEffect"]
    ];
});
_c = MenuItem;
const __TURBOPACK__default__export__ = /*#__PURE__*/ _c1 = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["forwardRef"])(MenuItem);
var _c, _c1;
__turbopack_refresh__.register(_c, "MenuItem");
__turbopack_refresh__.register(_c1, "%default%");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/components/vertical-menu/MenuItem.tsx [app-client] (ecmascript) <export default as MenuItem>": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, t: __turbopack_require_real__ } = __turbopack_context__;
{
__turbopack_esm__({
    "MenuItem": (()=>__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$MenuItem$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])
});
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$MenuItem$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/MenuItem.tsx [app-client] (ecmascript)");
}}),
"[project]/src/@menu/components/vertical-menu/SubMenu.tsx [app-client] (ecmascript) <export default as SubMenu>": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, t: __turbopack_require_real__ } = __turbopack_context__;
{
__turbopack_esm__({
    "SubMenu": (()=>__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$SubMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])
});
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$SubMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/SubMenu.tsx [app-client] (ecmascript)");
}}),
"[project]/src/@menu/styles/StyledMenuIcon.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
;
const StyledMenuIcon = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].span`
  display: flex;
  align-items: center;
  justify-content: center;
  margin-inline-end: 10px;
  ${({ rootStyles })=>rootStyles};
`;
const __TURBOPACK__default__export__ = StyledMenuIcon;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/StyledMenuSectionLabel.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
;
const StyledMenuSectionLabel = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].span`
  ${({ textTruncate })=>textTruncate && `
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
    `};
  ${({ isCollapsed, isHovered })=>!isCollapsed || isCollapsed && isHovered ? `
flex-grow: 1;
` : ''}
  ${({ rootStyles })=>rootStyles};
`;
const __TURBOPACK__default__export__ = StyledMenuSectionLabel;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/vertical/StyledVerticalMenuSection.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
;
;
const StyledVerticalMenuSection = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].li`
  display: flex;
  inline-size: 100%;
  position: relative;
  overflow: hidden;
  margin-block-start: 15px;

  & .${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].menuSectionContent} {
    font-size: 14px;
    color: #aaaaaa;
  }

  ${({ menuSectionStyles })=>menuSectionStyles};
  ${({ rootStyles })=>rootStyles};
`;
const __TURBOPACK__default__export__ = StyledVerticalMenuSection;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/components/vertical-menu/MenuSection.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
// React Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Third-party Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/classnames/index.js [app-client] (ecmascript)");
// Hook Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/hooks/useVerticalNav.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/hooks/useVerticalMenu.tsx [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
// Styled Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuIcon$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledMenuIcon.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuPrefix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledMenuPrefix.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuSuffix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledMenuSuffix.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuSectionLabel$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledMenuSectionLabel.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$vertical$2f$StyledVerticalMenuSection$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/vertical/StyledVerticalMenuSection.tsx [app-client] (ecmascript)");
;
var _s = __turbopack_refresh__.signature();
'use client';
;
;
;
;
;
;
;
;
;
;
const menuSectionWrapperStyles = {
    display: 'inline-block',
    inlineSize: '100%',
    position: 'relative',
    listStyle: 'none',
    padding: 0,
    overflow: 'hidden'
};
const menuSectionContentStyles = {
    display: 'flex',
    alignItems: 'center',
    inlineSize: '100%',
    position: 'relative',
    paddingBlock: '0.75rem',
    paddingInline: '1.25rem',
    overflow: 'hidden'
};
const MenuSection = (props, ref)=>{
    _s();
    // Props
    const { children, icon, className, prefix, suffix, label, rootStyles, ...rest } = props;
    // Hooks
    const { isCollapsed, isHovered } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])();
    const { menuSectionStyles, collapsedMenuSectionLabel, textTruncate } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])();
    const getMenuSectionStyles = (element)=>{
        // If the menuSectionStyles prop is provided, get the styles for the element from the prop
        if (menuSectionStyles) {
            return menuSectionStyles[element];
        }
    };
    return(// eslint-disable-next-line lines-around-comment
    // Menu Section
    /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$vertical$2f$StyledVerticalMenuSection$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
        ref: ref,
        rootStyles: rootStyles,
        menuSectionStyles: getMenuSectionStyles('root'),
        className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].menuSectionRoot, className),
        children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("ul", {
            className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].menuSectionWrapper,
            ...rest,
            style: menuSectionWrapperStyles,
            children: [
                /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("li", {
                    className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].menuSectionContent,
                    style: menuSectionContentStyles,
                    children: [
                        icon && /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuIcon$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                            className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].icon,
                            rootStyles: getMenuSectionStyles('icon'),
                            children: icon
                        }, void 0, false, {
                            fileName: "[project]/src/@menu/components/vertical-menu/MenuSection.tsx",
                            lineNumber: 92,
                            columnNumber: 13
                        }, this),
                        prefix && /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuPrefix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                            isCollapsed: isCollapsed,
                            className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].prefix,
                            rootStyles: getMenuSectionStyles('prefix'),
                            children: prefix
                        }, void 0, false, {
                            fileName: "[project]/src/@menu/components/vertical-menu/MenuSection.tsx",
                            lineNumber: 97,
                            columnNumber: 13
                        }, this),
                        collapsedMenuSectionLabel && isCollapsed && !isHovered ? /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuSectionLabel$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                            isCollapsed: isCollapsed,
                            isHovered: isHovered,
                            className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].menuSectionLabel,
                            rootStyles: getMenuSectionStyles('label'),
                            textTruncate: textTruncate,
                            children: collapsedMenuSectionLabel
                        }, void 0, false, {
                            fileName: "[project]/src/@menu/components/vertical-menu/MenuSection.tsx",
                            lineNumber: 106,
                            columnNumber: 13
                        }, this) : label && /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuSectionLabel$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                            isCollapsed: isCollapsed,
                            isHovered: isHovered,
                            className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].menuSectionLabel,
                            rootStyles: getMenuSectionStyles('label'),
                            textTruncate: textTruncate,
                            children: label
                        }, void 0, false, {
                            fileName: "[project]/src/@menu/components/vertical-menu/MenuSection.tsx",
                            lineNumber: 117,
                            columnNumber: 15
                        }, this),
                        suffix && /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuSuffix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                            isCollapsed: isCollapsed,
                            className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].suffix,
                            rootStyles: getMenuSectionStyles('suffix'),
                            children: suffix
                        }, void 0, false, {
                            fileName: "[project]/src/@menu/components/vertical-menu/MenuSection.tsx",
                            lineNumber: 129,
                            columnNumber: 13
                        }, this)
                    ]
                }, void 0, true, {
                    fileName: "[project]/src/@menu/components/vertical-menu/MenuSection.tsx",
                    lineNumber: 90,
                    columnNumber: 9
                }, this),
                children
            ]
        }, void 0, true, {
            fileName: "[project]/src/@menu/components/vertical-menu/MenuSection.tsx",
            lineNumber: 88,
            columnNumber: 7
        }, this)
    }, void 0, false, {
        fileName: "[project]/src/@menu/components/vertical-menu/MenuSection.tsx",
        lineNumber: 81,
        columnNumber: 5
    }, this));
};
_s(MenuSection, "2e3EGotqxSieFaP6puNAQB6QWM8=", false, function() {
    return [
        __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"],
        __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"]
    ];
});
_c = MenuSection;
const __TURBOPACK__default__export__ = /*#__PURE__*/ _c1 = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["forwardRef"])(MenuSection);
var _c, _c1;
__turbopack_refresh__.register(_c, "MenuSection");
__turbopack_refresh__.register(_c1, "%default%");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/components/vertical-menu/MenuSection.tsx [app-client] (ecmascript) <export default as MenuSection>": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, t: __turbopack_require_real__ } = __turbopack_context__;
{
__turbopack_esm__({
    "MenuSection": (()=>__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$MenuSection$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])
});
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$MenuSection$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/MenuSection.tsx [app-client] (ecmascript)");
}}),
"[project]/src/@menu/components/horizontal-menu/MenuItem.tsx [app-client] (ecmascript) <export default as MenuItem>": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, t: __turbopack_require_real__ } = __turbopack_context__;
{
__turbopack_esm__({
    "MenuItem": (()=>__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$MenuItem$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])
});
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$MenuItem$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/MenuItem.tsx [app-client] (ecmascript)");
}}),
"[project]/src/@menu/components/horizontal-menu/SubMenu.tsx [app-client] (ecmascript) <export default as SubMenu>": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, t: __turbopack_require_real__ } = __turbopack_context__;
{
__turbopack_esm__({
    "SubMenu": (()=>__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$SubMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])
});
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$SubMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/SubMenu.tsx [app-client] (ecmascript)");
}}),
"[project]/src/@menu/components/horizontal-menu/Menu.tsx [app-client] (ecmascript) <export default as Menu>": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, t: __turbopack_require_real__ } = __turbopack_context__;
{
__turbopack_esm__({
    "Menu": (()=>__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$Menu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])
});
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$Menu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/Menu.tsx [app-client] (ecmascript)");
}}),
"[project]/src/@menu/components/vertical-menu/Menu.tsx [app-client] (ecmascript) <export default as Menu>": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, t: __turbopack_require_real__ } = __turbopack_context__;
{
__turbopack_esm__({
    "Menu": (()=>__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$Menu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])
});
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$Menu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/Menu.tsx [app-client] (ecmascript)");
}}),
"[project]/src/@menu/utils/menuUtils.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// React Imports
__turbopack_esm__({
    "confirmUrlInChildren": (()=>confirmUrlInChildren),
    "mapHorizontalToVerticalMenu": (()=>mapHorizontalToVerticalMenu),
    "renderMenuIcon": (()=>renderMenuIcon)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$horizontal$2d$menu$2f$index$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$module__evaluation$3e$__ = __turbopack_import__("[project]/src/@menu/horizontal-menu/index.tsx [app-client] (ecmascript) <module evaluation>");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$vertical$2d$menu$2f$index$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$module__evaluation$3e$__ = __turbopack_import__("[project]/src/@menu/vertical-menu/index.tsx [app-client] (ecmascript) <module evaluation>");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f$components$2f$GenerateMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/components/GenerateMenu.tsx [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
// Styled Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuIcon$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledMenuIcon.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$MenuItem$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__MenuItem$3e$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/MenuItem.tsx [app-client] (ecmascript) <export default as MenuItem>");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$MenuItem$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__MenuItem$3e$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/MenuItem.tsx [app-client] (ecmascript) <export default as MenuItem>");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$SubMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__SubMenu$3e$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/SubMenu.tsx [app-client] (ecmascript) <export default as SubMenu>");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$SubMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__SubMenu$3e$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/SubMenu.tsx [app-client] (ecmascript) <export default as SubMenu>");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$Menu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__Menu$3e$__ = __turbopack_import__("[project]/src/@menu/components/horizontal-menu/Menu.tsx [app-client] (ecmascript) <export default as Menu>");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$Menu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__Menu$3e$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/Menu.tsx [app-client] (ecmascript) <export default as Menu>");
;
;
;
;
;
;
;
const confirmUrlInChildren = (children, url)=>{
    if (!children) {
        return false;
    }
    if (Array.isArray(children)) {
        return children.some((child)=>confirmUrlInChildren(child, url));
    }
    if (/*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["isValidElement"])(children)) {
        const { component, href, exactMatch, activeUrl, children: subChildren } = children.props;
        if (component && component.props.href) {
            return exactMatch === true || exactMatch === undefined ? component.props.href === url : activeUrl && url.includes(activeUrl);
        }
        if (href) {
            return exactMatch === true || exactMatch === undefined ? href === url : activeUrl && url.includes(activeUrl);
        }
        if (subChildren) {
            return confirmUrlInChildren(subChildren, url);
        }
    }
    return false;
};
/*
 * Reason behind mapping the children of the horizontal-menu component to the vertical-menu component:
 * The Horizontal menu components will not work inside of Vertical menu on small screens.
 * So, we have to map the children of the horizontal-menu components to the vertical-menu components.
 * We also kept the same names and almost similar props for menuitem and submenu components for easy mapping.
 */ /**
 * Processes children of a HorizontalMenu component to either generate a vertical menu directly
 * from menuData or apply a transformation function to each child.
 *
 * @param {ReactNode} children - The children of the HorizontalMenu component.
 * @param {Function} mapFunction - A function to transform each child that doesn't have menuData.
 * @returns {ReactNode} The processed children suitable for inclusion in a VerticalMenu.
 */ const processMenuChildren = (children, mapFunction)=>{
    return __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["Children"].map(children, (child)=>{
        // Skip processing for non-React elements
        if (!/*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["isValidElement"])(child)) return child;
        // If child has menuData prop, create a GenerateVerticalMenu component
        // Otherwise, apply the transformation function to the child
        return child.props?.menuData ? /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f$components$2f$GenerateMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["GenerateVerticalMenu"], {
            menuData: child.props.menuData
        }, void 0, false, {
            fileName: "[project]/src/@menu/utils/menuUtils.tsx",
            lineNumber: 88,
            columnNumber: 36
        }, this) : mapFunction(child);
    });
};
const mapHorizontalToVerticalMenu = (children)=>{
    return __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["Children"].map(children, (child)=>{
        // If the child is not a valid React element, exclude it from the output
        if (!/*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["isValidElement"])(child)) return null;
        // Destructure to separate specific props and rest props for further use
        const { children: childChildren, verticalMenuProps, ...rest } = child.props;
        // Use a switch statement to handle different types of menu items
        switch(child.type){
            case __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$MenuItem$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__MenuItem$3e$__["MenuItem"]:
                // Directly transform HorizontalMenuItem to VerticalMenuItem
                return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$MenuItem$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__MenuItem$3e$__["MenuItem"], {
                    ...rest,
                    children: childChildren
                }, void 0, false, {
                    fileName: "[project]/src/@menu/utils/menuUtils.tsx",
                    lineNumber: 111,
                    columnNumber: 16
                }, this);
            case __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$SubMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__SubMenu$3e$__["SubMenu"]:
                // Transform HorizontalSubMenu to VerticalSubMenu, recursively transforming its children
                return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$SubMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__SubMenu$3e$__["SubMenu"], {
                    ...rest,
                    children: mapHorizontalToVerticalMenu(childChildren)
                }, void 0, false, {
                    fileName: "[project]/src/@menu/utils/menuUtils.tsx",
                    lineNumber: 114,
                    columnNumber: 16
                }, this);
            case __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$horizontal$2d$menu$2f$Menu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__Menu$3e$__["Menu"]:
                // For HorizontalMenu, process its children specifically, then wrap in VerticalMenu
                const transformedChildren = processMenuChildren(childChildren, mapHorizontalToVerticalMenu);
                return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$Menu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$export__default__as__Menu$3e$__["Menu"], {
                    ...verticalMenuProps,
                    children: transformedChildren
                }, void 0, false, {
                    fileName: "[project]/src/@menu/utils/menuUtils.tsx",
                    lineNumber: 119,
                    columnNumber: 16
                }, this);
            default:
                // For any other type of child, return it without modification
                return child;
        }
    });
};
const renderMenuIcon = (params)=>{
    const { icon, level, active, disabled, styles, renderExpandedMenuItemIcon, isBreakpointReached } = params;
    if (icon && (level === 0 || !isBreakpointReached && level && level > 0)) {
        return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuIcon$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
            className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].icon,
            rootStyles: styles,
            children: icon
        }, void 0, false, {
            fileName: "[project]/src/@menu/utils/menuUtils.tsx",
            lineNumber: 135,
            columnNumber: 7
        }, this);
    }
    if (level && level !== 0 && renderExpandedMenuItemIcon && renderExpandedMenuItemIcon.icon !== null && (!renderExpandedMenuItemIcon.level || renderExpandedMenuItemIcon.level >= level)) {
        const iconToRender = typeof renderExpandedMenuItemIcon.icon === 'function' ? renderExpandedMenuItemIcon.icon({
            level,
            active,
            disabled
        }) : renderExpandedMenuItemIcon.icon;
        if (iconToRender) {
            return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuIcon$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].icon,
                rootStyles: styles,
                children: iconToRender
            }, void 0, false, {
                fileName: "[project]/src/@menu/utils/menuUtils.tsx",
                lineNumber: 155,
                columnNumber: 9
            }, this);
        }
    }
    return null;
};
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/styles/vertical/StyledVerticalNavExpandIcon.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "StyledVerticalNavExpandIconWrapper": (()=>StyledVerticalNavExpandIconWrapper),
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
;
const StyledVerticalNavExpandIconWrapper = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].span`
  display: flex;
  margin-inline-start: 5px;
  ${({ rootStyles })=>rootStyles};
`;
const StyledVerticalNavExpandIcon = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].span`
  display: flex;

  & > i,
  & > svg {
    transition: ${({ transitionDuration })=>`transform ${transitionDuration}ms ease-in-out`};
    ${({ open })=>open && 'transform: rotate(90deg);'}

    [dir='rtl'] & {
      transform: rotate(180deg);
      ${({ open })=>open && 'transform: rotate(90deg);'}
    }
  }
`;
const __TURBOPACK__default__export__ = StyledVerticalNavExpandIcon;
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/components/vertical-menu/SubMenu.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
// React Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/index.js [app-client] (ecmascript)");
// Next Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$navigation$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/navigation.js [app-client] (ecmascript)");
// Third-party Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/classnames/index.js [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
// Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$SubMenuContent$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/SubMenuContent.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$MenuButton$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/MenuButton.tsx [app-client] (ecmascript)");
// Icon Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$svg$2f$ChevronRight$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/svg/ChevronRight.tsx [app-client] (ecmascript)");
// Hook Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/hooks/useVerticalNav.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/hooks/useVerticalMenu.tsx [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuUtils$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuUtils.tsx [app-client] (ecmascript)");
// Styled Component Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuLabel$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledMenuLabel.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuPrefix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledMenuPrefix.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuSuffix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/StyledMenuSuffix.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$vertical$2f$StyledVerticalNavExpandIcon$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/styles/vertical/StyledVerticalNavExpandIcon.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__ = __turbopack_import__("[project]/node_modules/@floating-ui/react/dist/floating-ui.react.mjs [app-client] (ecmascript) <locals>");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2d$dom$2f$dist$2f$floating$2d$ui$2e$react$2d$dom$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__ = __turbopack_import__("[project]/node_modules/@floating-ui/react-dom/dist/floating-ui.react-dom.mjs [app-client] (ecmascript) <locals>");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$dom$2f$dist$2f$floating$2d$ui$2e$dom$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__ = __turbopack_import__("[project]/node_modules/@floating-ui/dom/dist/floating-ui.dom.mjs [app-client] (ecmascript) <locals>");
;
var _s = __turbopack_refresh__.signature();
'use client';
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
;
const StyledSubMenu = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].li`
  position: relative;
  inline-size: 100%;
  margin-block-start: 4px;

  &.${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].open} > .${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].button} {
    background-color: #f3f3f3;
  }

  ${({ menuItemStyles })=>menuItemStyles};
  ${({ rootStyles })=>rootStyles};

  > .${__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].button} {
    ${({ level, disabled, active, children, isCollapsed, isPopoutWhenCollapsed })=>(0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$MenuButton$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuButtonStyles"])({
        level,
        active,
        disabled,
        children,
        isCollapsed,
        isPopoutWhenCollapsed
    })};
    ${({ buttonStyles })=>buttonStyles};
  }
`;
_c = StyledSubMenu;
const SubMenu = (props, ref)=>{
    _s();
    // Props
    const { children, className, contentClassName, label, icon, title, prefix, suffix, defaultOpen, level = 0, disabled = false, rootStyles, component, onOpenChange, onClick, onKeyUp, ...rest } = props;
    // States
    const [openWhenCollapsed, setOpenWhenCollapsed] = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useState"])(false);
    const [active, setActive] = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useState"])(false);
    // Refs
    const contentRef = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useRef"])(null);
    // Hooks
    const id = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useId"])();
    const pathname = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$navigation$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["usePathname"])();
    const { isCollapsed, isPopoutWhenCollapsed, isHovered, isBreakpointReached } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])();
    const tree = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useFloatingTree"])();
    const { browserScroll, triggerPopout, renderExpandIcon, renderExpandedMenuItemIcon, menuItemStyles, openSubmenu, toggleOpenSubmenu, transitionDuration, openSubmenusRef, popoutMenuOffset, textTruncate } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])();
    // Vars
    // Filter out falsy values from children
    const childNodes = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["Children"].toArray(children).filter(Boolean);
    const mainAxisOffset = popoutMenuOffset && popoutMenuOffset.mainAxis && (typeof popoutMenuOffset.mainAxis === 'function' ? popoutMenuOffset.mainAxis({
        level
    }) : popoutMenuOffset.mainAxis);
    const alignmentAxisOffset = popoutMenuOffset && popoutMenuOffset.alignmentAxis && (typeof popoutMenuOffset.alignmentAxis === 'function' ? popoutMenuOffset.alignmentAxis({
        level
    }) : popoutMenuOffset.alignmentAxis);
    const { refs, floatingStyles, context } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useFloating"])({
        strategy: 'fixed',
        open: openWhenCollapsed,
        onOpenChange: setOpenWhenCollapsed,
        placement: 'right-start',
        middleware: [
            (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2d$dom$2f$dist$2f$floating$2d$ui$2e$react$2d$dom$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["offset"])({
                mainAxis: mainAxisOffset,
                alignmentAxis: alignmentAxisOffset
            }),
            (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2d$dom$2f$dist$2f$floating$2d$ui$2e$react$2d$dom$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["flip"])({
                crossAxis: false
            }),
            (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2d$dom$2f$dist$2f$floating$2d$ui$2e$react$2d$dom$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["shift"])(),
            (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2d$dom$2f$dist$2f$floating$2d$ui$2e$react$2d$dom$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["hide"])()
        ],
        whileElementsMounted: __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$dom$2f$dist$2f$floating$2d$ui$2e$dom$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["autoUpdate"]
    });
    const hover = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useHover"])(context, {
        handleClose: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["safePolygon"])({
            blockPointerEvents: true
        }),
        restMs: 25,
        enabled: triggerPopout === 'hover',
        delay: {
            open: 75
        } // Delay opening submenu by 75ms
    });
    const click = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useClick"])(context, {
        enabled: triggerPopout === 'click' // Only enable click effect when triggerPopout option is set to 'click'
    });
    const dismiss = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useDismiss"])(context);
    const role = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useRole"])(context, {
        role: 'menu'
    });
    // Merge all the interactions into prop getters
    const { getReferenceProps, getFloatingProps, getItemProps } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useInteractions"])([
        hover,
        click,
        dismiss,
        role
    ]);
    const isSubMenuOpen = openSubmenu?.some((item)=>item.id === id) ?? false;
    const handleSlideToggle = ()=>{
        if (level === 0 && isCollapsed && !isHovered) {
            return;
        }
        toggleOpenSubmenu?.({
            level,
            label,
            active,
            id
        });
        onOpenChange?.(!isSubMenuOpen);
        if (openSubmenusRef?.current && openSubmenusRef?.current.length > 0) openSubmenusRef.current = [];
    };
    const handleOnClick = (event)=>{
        onClick?.(event);
        handleSlideToggle();
    };
    const handleOnKeyUp = (event)=>{
        onKeyUp?.(event);
        if (event.key === 'Enter') {
            handleSlideToggle();
        }
    };
    const getSubMenuItemStyles = (element)=>{
        // If the menuItemStyles prop is provided, get the styles for the specified element.
        if (menuItemStyles) {
            // Define the parameters that are passed to the style functions.
            const params = {
                level,
                disabled,
                active,
                isSubmenu: true,
                open: isSubMenuOpen
            };
            // Get the style function for the specified element.
            const styleFunction = menuItemStyles[element];
            if (styleFunction) {
                // If the style function is a function, call it and return the result.
                // Otherwise, return the style function itself.
                return typeof styleFunction === 'function' ? styleFunction(params) : styleFunction;
            }
        }
    };
    // Event emitter allows you to communicate across tree components.
    // This effect closes all menus when an item gets clicked anywhere in the tree.
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "SubMenu.useEffect": ()=>{
            const handleTreeClick = {
                "SubMenu.useEffect.handleTreeClick": ()=>{
                    setOpenWhenCollapsed(false);
                }
            }["SubMenu.useEffect.handleTreeClick"];
            tree?.events.on('click', handleTreeClick);
            return ({
                "SubMenu.useEffect": ()=>{
                    tree?.events.off('click', handleTreeClick);
                }
            })["SubMenu.useEffect"];
        }
    }["SubMenu.useEffect"], [
        tree
    ]);
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useLayoutEffect"])({
        "SubMenu.useLayoutEffect": ()=>{
            if (isCollapsed && level === 0) {
                setOpenWhenCollapsed(false);
            }
        }
    }["SubMenu.useLayoutEffect"], [
        isCollapsed,
        level,
        active
    ]);
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "SubMenu.useEffect": ()=>{
            if ((0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuUtils$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["confirmUrlInChildren"])(children, pathname)) {
                openSubmenusRef?.current.push({
                    level,
                    label,
                    active: true,
                    id
                });
            } else {
                if (defaultOpen) {
                    openSubmenusRef?.current.push({
                        level,
                        label,
                        active: false,
                        id
                    });
                }
            }
        // eslint-disable-next-line react-hooks/exhaustive-deps
        }
    }["SubMenu.useEffect"], []);
    // Change active state when the url changes
    (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useEffect"])({
        "SubMenu.useEffect": ()=>{
            // Check if the current url matches any of the children urls
            if ((0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuUtils$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["confirmUrlInChildren"])(children, pathname)) {
                setActive(true);
                if (openSubmenusRef?.current.findIndex({
                    "SubMenu.useEffect": (submenu)=>submenu.id === id
                }["SubMenu.useEffect"]) === -1) {
                    openSubmenusRef?.current.push({
                        level,
                        label,
                        active: true,
                        id
                    });
                }
            } else {
                setActive(false);
            }
        // eslint-disable-next-line react-hooks/exhaustive-deps
        }
    }["SubMenu.useEffect"], [
        pathname
    ]);
    /* useEffect(() => {
    console.log(openSubmenu)
  }, [openSubmenu]) */ const submenuContent = /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$SubMenuContent$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
        ref: isCollapsed && level === 0 && isPopoutWhenCollapsed ? refs.setFloating : contentRef,
        ...isCollapsed && level === 0 && isPopoutWhenCollapsed && getFloatingProps(),
        browserScroll: browserScroll,
        openWhenCollapsed: openWhenCollapsed,
        isPopoutWhenCollapsed: isPopoutWhenCollapsed,
        transitionDuration: transitionDuration,
        open: isSubMenuOpen,
        level: level,
        isCollapsed: isCollapsed,
        isHovered: isHovered,
        className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].subMenuContent, contentClassName),
        rootStyles: {
            ...isCollapsed && level === 0 && isPopoutWhenCollapsed && floatingStyles,
            ...getSubMenuItemStyles('subMenuContent')
        },
        children: childNodes.map((node)=>/*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["cloneElement"])(node, {
                ...getItemProps({
                    onClick (event) {
                        if (node.props.children && !Array.isArray(node.props.children)) {
                            node.props.onClick?.(event);
                            tree?.events.emit('click');
                        }
                    }
                }),
                level: level + 1
            }))
    }, void 0, false, {
        fileName: "[project]/src/@menu/components/vertical-menu/SubMenu.tsx",
        lineNumber: 320,
        columnNumber: 5
    }, this);
    return(// eslint-disable-next-line lines-around-comment
    /* Sub Menu */ /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(StyledSubMenu, {
        ref: ref,
        className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].subMenuRoot, {
            [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].active]: active
        }, {
            [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].disabled]: disabled
        }, {
            [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].open]: isSubMenuOpen
        }, className),
        menuItemStyles: getSubMenuItemStyles('root'),
        level: level,
        isPopoutWhenCollapsed: isPopoutWhenCollapsed,
        disabled: disabled,
        active: active,
        isCollapsed: isCollapsed,
        buttonStyles: getSubMenuItemStyles('button'),
        rootStyles: rootStyles,
        children: [
            /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$MenuButton$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                ref: isCollapsed && level === 0 && isPopoutWhenCollapsed && !disabled ? refs.setReference : null,
                onClick: handleOnClick,
                ...isCollapsed && level === 0 && isPopoutWhenCollapsed && !disabled && getReferenceProps(),
                onKeyUp: handleOnKeyUp,
                title: title,
                className: (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$classnames$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].button, {
                    [__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].active]: active
                }),
                component: component,
                tabIndex: disabled ? -1 : 0,
                ...rest,
                children: [
                    (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuUtils$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["renderMenuIcon"])({
                        icon,
                        level,
                        active,
                        disabled,
                        renderExpandedMenuItemIcon,
                        styles: getSubMenuItemStyles('icon'),
                        isBreakpointReached
                    }),
                    prefix && /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuPrefix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                        isHovered: isHovered,
                        isCollapsed: isCollapsed,
                        firstLevel: level === 0,
                        className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].prefix,
                        rootStyles: getSubMenuItemStyles('prefix'),
                        children: prefix
                    }, void 0, false, {
                        fileName: "[project]/src/@menu/components/vertical-menu/SubMenu.tsx",
                        lineNumber: 399,
                        columnNumber: 11
                    }, this),
                    /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuLabel$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                        className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].label,
                        rootStyles: getSubMenuItemStyles('label'),
                        textTruncate: textTruncate,
                        children: label
                    }, void 0, false, {
                        fileName: "[project]/src/@menu/components/vertical-menu/SubMenu.tsx",
                        lineNumber: 411,
                        columnNumber: 9
                    }, this),
                    suffix && /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$StyledMenuSuffix$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                        isHovered: isHovered,
                        isCollapsed: isCollapsed,
                        firstLevel: level === 0,
                        className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].suffix,
                        rootStyles: getSubMenuItemStyles('suffix'),
                        children: suffix
                    }, void 0, false, {
                        fileName: "[project]/src/@menu/components/vertical-menu/SubMenu.tsx",
                        lineNumber: 421,
                        columnNumber: 11
                    }, this),
                    isCollapsed && !isHovered && level === 0 ? null : /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$vertical$2f$StyledVerticalNavExpandIcon$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["StyledVerticalNavExpandIconWrapper"], {
                        className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["menuClasses"].subMenuExpandIcon,
                        rootStyles: getSubMenuItemStyles('subMenuExpandIcon'),
                        children: renderExpandIcon ? renderExpandIcon({
                            level,
                            disabled,
                            active,
                            open: isSubMenuOpen
                        }) : // eslint-disable-next-line lines-around-comment
                        /* Expanded Arrow Icon */ /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$styles$2f$vertical$2f$StyledVerticalNavExpandIcon$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                            open: isSubMenuOpen,
                            transitionDuration: transitionDuration,
                            children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$svg$2f$ChevronRight$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {
                                fontSize: "1rem"
                            }, void 0, false, {
                                fileName: "[project]/src/@menu/components/vertical-menu/SubMenu.tsx",
                                lineNumber: 449,
                                columnNumber: 17
                            }, this)
                        }, void 0, false, {
                            fileName: "[project]/src/@menu/components/vertical-menu/SubMenu.tsx",
                            lineNumber: 448,
                            columnNumber: 15
                        }, this)
                    }, void 0, false, {
                        fileName: "[project]/src/@menu/components/vertical-menu/SubMenu.tsx",
                        lineNumber: 434,
                        columnNumber: 11
                    }, this)
                ]
            }, void 0, true, {
                fileName: "[project]/src/@menu/components/vertical-menu/SubMenu.tsx",
                lineNumber: 375,
                columnNumber: 7
            }, this),
            isCollapsed && level === 0 && isPopoutWhenCollapsed ? /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["FloatingPortal"], {
                children: openWhenCollapsed && submenuContent
            }, void 0, false, {
                fileName: "[project]/src/@menu/components/vertical-menu/SubMenu.tsx",
                lineNumber: 458,
                columnNumber: 9
            }, this) : submenuContent
        ]
    }, void 0, true, {
        fileName: "[project]/src/@menu/components/vertical-menu/SubMenu.tsx",
        lineNumber: 356,
        columnNumber: 5
    }, this));
};
_s(SubMenu, "vIE6+04OuBRzOdUwS3Fp0eX6rq8=", false, function() {
    return [
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["useId"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$navigation$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["usePathname"],
        __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useFloatingTree"],
        __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useFloating"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useHover"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useClick"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useDismiss"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useRole"],
        __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$floating$2d$ui$2f$react$2f$dist$2f$floating$2d$ui$2e$react$2e$mjs__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__["useInteractions"]
    ];
});
_c1 = SubMenu;
const __TURBOPACK__default__export__ = /*#__PURE__*/ _c2 = (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$index$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["forwardRef"])(SubMenu);
var _c, _c1, _c2;
__turbopack_refresh__.register(_c, "StyledSubMenu");
__turbopack_refresh__.register(_c1, "SubMenu");
__turbopack_refresh__.register(_c2, "%default%");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/components/vertical-menu/NavHeader.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// Third-party Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/@emotion/styled/dist/emotion-styled.browser.development.esm.js [app-client] (ecmascript)");
// Hook Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/hooks/useVerticalNav.tsx [app-client] (ecmascript)");
// Util Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/utils/menuClasses.ts [app-client] (ecmascript)");
;
var _s = __turbopack_refresh__.signature();
;
;
;
const StyledNavHeader = __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f40$emotion$2f$styled$2f$dist$2f$emotion$2d$styled$2e$browser$2e$development$2e$esm$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"].div`
  padding: 15px;
  padding-inline-start: 20px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  transition: ${({ transitionDuration })=>`padding-inline ${transitionDuration}ms ease-in-out`};

  ${({ isHovered, isCollapsed, collapsedWidth })=>isCollapsed && !isHovered && `padding-inline: calc((${collapsedWidth}px - 1px - 22px) / 2);`}
`;
_c = StyledNavHeader;
const NavHeader = ({ children })=>{
    _s();
    // Hooks
    const { isHovered, isCollapsed, collapsedWidth, transitionDuration } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])();
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(StyledNavHeader, {
        className: __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$utils$2f$menuClasses$2e$ts__$5b$app$2d$client$5d$__$28$ecmascript$29$__["verticalNavClasses"].header,
        isHovered: isHovered,
        isCollapsed: isCollapsed,
        collapsedWidth: collapsedWidth,
        transitionDuration: transitionDuration,
        children: children
    }, void 0, false, {
        fileName: "[project]/src/@menu/components/vertical-menu/NavHeader.tsx",
        lineNumber: 38,
        columnNumber: 5
    }, this);
};
_s(NavHeader, "z3FrXfiqLu8S7UfKojL5OYCgeUw=", false, function() {
    return [
        __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"]
    ];
});
_c1 = NavHeader;
const __TURBOPACK__default__export__ = NavHeader;
var _c, _c1;
__turbopack_refresh__.register(_c, "StyledNavHeader");
__turbopack_refresh__.register(_c1, "NavHeader");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/svg/Close.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// React Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
;
const Close = (props)=>{
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("svg", {
        xmlns: "http://www.w3.org/2000/svg",
        width: "1em",
        height: "1em",
        fontSize: "1.5rem",
        viewBox: "0 0 24 24",
        ...props,
        children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("path", {
            fill: "currentColor",
            d: "m16.192 6.344-4.243 4.242-4.242-4.242-1.414 1.414L10.535 12l-4.242 4.242 1.414 1.414 4.242-4.242 4.243 4.242 1.414-1.414L13.364 12l4.242-4.242z"
        }, void 0, false, {
            fileName: "[project]/src/@menu/svg/Close.tsx",
            lineNumber: 7,
            columnNumber: 7
        }, this)
    }, void 0, false, {
        fileName: "[project]/src/@menu/svg/Close.tsx",
        lineNumber: 6,
        columnNumber: 5
    }, this);
};
_c = Close;
const __TURBOPACK__default__export__ = Close;
var _c;
__turbopack_refresh__.register(_c, "Close");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/svg/RadioCircle.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// React Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
;
const RadioCircle = (props)=>{
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("svg", {
        xmlns: "http://www.w3.org/2000/svg",
        width: "1em",
        height: "1em",
        fontSize: "1.5rem",
        viewBox: "0 0 24 24",
        ...props,
        children: /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("path", {
            fill: "currentColor",
            d: "M5 12c0 3.859 3.14 7 7 7 3.859 0 7-3.141 7-7s-3.141-7-7-7c-3.86 0-7 3.141-7 7zm12 0c0 2.757-2.243 5-5 5s-5-2.243-5-5 2.243-5 5-5 5 2.243 5 5z"
        }, void 0, false, {
            fileName: "[project]/src/@menu/svg/RadioCircle.tsx",
            lineNumber: 7,
            columnNumber: 7
        }, this)
    }, void 0, false, {
        fileName: "[project]/src/@menu/svg/RadioCircle.tsx",
        lineNumber: 6,
        columnNumber: 5
    }, this);
};
_c = RadioCircle;
const __TURBOPACK__default__export__ = RadioCircle;
var _c;
__turbopack_refresh__.register(_c, "RadioCircle");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/svg/RadioCircleMarked.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
// React Imports
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
;
const RadioCircleMarked = (props)=>{
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("svg", {
        xmlns: "http://www.w3.org/2000/svg",
        width: "1em",
        height: "1em",
        fontSize: "1.5rem",
        viewBox: "0 0 24 24",
        ...props,
        children: [
            /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("path", {
                fill: "currentColor",
                d: "M12 5c-3.859 0-7 3.141-7 7s3.141 7 7 7 7-3.141 7-7-3.141-7-7-7zm0 12c-2.757 0-5-2.243-5-5s2.243-5 5-5 5 2.243 5 5-2.243 5-5 5z"
            }, void 0, false, {
                fileName: "[project]/src/@menu/svg/RadioCircleMarked.tsx",
                lineNumber: 7,
                columnNumber: 7
            }, this),
            /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("path", {
                fill: "currentColor",
                d: "M12 9c-1.627 0-3 1.373-3 3s1.373 3 3 3 3-1.373 3-3-1.373-3-3-3z"
            }, void 0, false, {
                fileName: "[project]/src/@menu/svg/RadioCircleMarked.tsx",
                lineNumber: 11,
                columnNumber: 7
            }, this)
        ]
    }, void 0, true, {
        fileName: "[project]/src/@menu/svg/RadioCircleMarked.tsx",
        lineNumber: 6,
        columnNumber: 5
    }, this);
};
_c = RadioCircleMarked;
const __TURBOPACK__default__export__ = RadioCircleMarked;
var _c;
__turbopack_refresh__.register(_c, "RadioCircleMarked");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/components/vertical-menu/NavCollapseIcons.tsx [app-client] (ecmascript)": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, k: __turbopack_refresh__, m: module, z: __turbopack_require_stub__ } = __turbopack_context__;
{
__turbopack_esm__({
    "default": (()=>__TURBOPACK__default__export__)
});
var __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/node_modules/next/dist/compiled/react/jsx-dev-runtime.js [app-client] (ecmascript)");
// Hook Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/hooks/useVerticalNav.tsx [app-client] (ecmascript)");
// Icon Imports
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$svg$2f$Close$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/svg/Close.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$svg$2f$RadioCircle$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/svg/RadioCircle.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$svg$2f$RadioCircleMarked$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/svg/RadioCircleMarked.tsx [app-client] (ecmascript)");
;
var _s = __turbopack_refresh__.signature();
'use client';
;
;
;
;
const NavCollapseIcons = (props)=>{
    _s();
    // Props
    const { closeIcon, lockedIcon, unlockedIcon, onClick, onClose, ...rest } = props;
    // Hooks
    const { isCollapsed, collapseVerticalNav, isBreakpointReached, toggleVerticalNav } = (0, __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])();
    // Handle Lock / Unlock Icon Buttons click
    const handleClick = (action)=>{
        // Setup the verticalNav to be locked or unlocked
        const collapse = action === 'lock' ? false : true;
        // Tell the verticalNav to lock or unlock
        collapseVerticalNav(collapse);
        // Call onClick function if passed
        onClick && onClick();
    };
    // Handle Close button click
    const handleClose = ()=>{
        // Close verticalNav using toggle verticalNav function
        toggleVerticalNav(false);
        // Call onClose function if passed
        onClose && onClose();
    };
    return /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["Fragment"], {
        children: isBreakpointReached ? /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("span", {
            role: "button",
            tabIndex: 0,
            style: {
                display: 'flex',
                cursor: 'pointer'
            },
            onClick: handleClose,
            ...rest,
            children: closeIcon ?? /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$svg$2f$Close$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {}, void 0, false, {
                fileName: "[project]/src/@menu/components/vertical-menu/NavCollapseIcons.tsx",
                lineNumber: 54,
                columnNumber: 25
            }, this)
        }, void 0, false, {
            fileName: "[project]/src/@menu/components/vertical-menu/NavCollapseIcons.tsx",
            lineNumber: 53,
            columnNumber: 9
        }, this) : isCollapsed ? /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("span", {
            role: "button",
            tabIndex: 0,
            style: {
                display: 'flex',
                cursor: 'pointer'
            },
            onClick: ()=>handleClick('lock'),
            ...rest,
            children: unlockedIcon ?? /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$svg$2f$RadioCircle$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {}, void 0, false, {
                fileName: "[project]/src/@menu/components/vertical-menu/NavCollapseIcons.tsx",
                lineNumber: 64,
                columnNumber: 28
            }, this)
        }, void 0, false, {
            fileName: "[project]/src/@menu/components/vertical-menu/NavCollapseIcons.tsx",
            lineNumber: 57,
            columnNumber: 9
        }, this) : /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])("span", {
            role: "button",
            tabIndex: 0,
            style: {
                display: 'flex',
                cursor: 'pointer'
            },
            onClick: ()=>handleClick('unlock'),
            ...rest,
            children: lockedIcon ?? /*#__PURE__*/ (0, __TURBOPACK__imported__module__$5b$project$5d2f$node_modules$2f$next$2f$dist$2f$compiled$2f$react$2f$jsx$2d$dev$2d$runtime$2e$js__$5b$app$2d$client$5d$__$28$ecmascript$29$__["jsxDEV"])(__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$svg$2f$RadioCircleMarked$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"], {}, void 0, false, {
                fileName: "[project]/src/@menu/components/vertical-menu/NavCollapseIcons.tsx",
                lineNumber: 74,
                columnNumber: 26
            }, this)
        }, void 0, false, {
            fileName: "[project]/src/@menu/components/vertical-menu/NavCollapseIcons.tsx",
            lineNumber: 67,
            columnNumber: 9
        }, this)
    }, void 0, false);
};
_s(NavCollapseIcons, "eMGhpsg9ilKQVb9UBk4RhHwoM+o=", false, function() {
    return [
        __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$hooks$2f$useVerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"]
    ];
});
_c = NavCollapseIcons;
const __TURBOPACK__default__export__ = NavCollapseIcons;
var _c;
__turbopack_refresh__.register(_c, "NavCollapseIcons");
if (typeof globalThis.$RefreshHelpers$ === 'object' && globalThis.$RefreshHelpers !== null) {
    __turbopack_refresh__.registerExports(module, globalThis.$RefreshHelpers$);
}
}}),
"[project]/src/@menu/vertical-menu/index.tsx [app-client] (ecmascript) <module evaluation>": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, t: __turbopack_require_real__ } = __turbopack_context__;
{
__turbopack_esm__({});
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$Menu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/Menu.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$SubMenu$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/SubMenu.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$MenuItem$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/MenuItem.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$NavHeader$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/NavHeader.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$VerticalNav$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/VerticalNav.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$MenuSection$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/MenuSection.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$NavCollapseIcons$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/NavCollapseIcons.tsx [app-client] (ecmascript)");
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$vertical$2d$menu$2f$index$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__$3c$locals$3e$__ = __turbopack_import__("[project]/src/@menu/vertical-menu/index.tsx [app-client] (ecmascript) <locals>");
}}),
"[project]/src/@menu/components/vertical-menu/NavHeader.tsx [app-client] (ecmascript) <export default as NavHeader>": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, t: __turbopack_require_real__ } = __turbopack_context__;
{
__turbopack_esm__({
    "NavHeader": (()=>__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$NavHeader$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])
});
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$NavHeader$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/NavHeader.tsx [app-client] (ecmascript)");
}}),
"[project]/src/@menu/components/vertical-menu/NavCollapseIcons.tsx [app-client] (ecmascript) <export default as NavCollapseIcons>": ((__turbopack_context__) => {
"use strict";

var { r: __turbopack_require__, f: __turbopack_module_context__, i: __turbopack_import__, s: __turbopack_esm__, v: __turbopack_export_value__, n: __turbopack_export_namespace__, c: __turbopack_cache__, M: __turbopack_modules__, l: __turbopack_load__, j: __turbopack_dynamic__, P: __turbopack_resolve_absolute_path__, U: __turbopack_relative_url__, R: __turbopack_resolve_module_id_path__, b: __turbopack_worker_blob_url__, g: global, __dirname, t: __turbopack_require_real__ } = __turbopack_context__;
{
__turbopack_esm__({
    "NavCollapseIcons": (()=>__TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$NavCollapseIcons$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__["default"])
});
var __TURBOPACK__imported__module__$5b$project$5d2f$src$2f40$menu$2f$components$2f$vertical$2d$menu$2f$NavCollapseIcons$2e$tsx__$5b$app$2d$client$5d$__$28$ecmascript$29$__ = __turbopack_import__("[project]/src/@menu/components/vertical-menu/NavCollapseIcons.tsx [app-client] (ecmascript)");
}}),
}]);

//# sourceMappingURL=src_%40menu_9ff34a._.js.map