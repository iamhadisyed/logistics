type SearchData = {
  id: string
  name: string
  url: string
  excludeLang?: boolean
  icon: string
  section: string
  shortcut?: string
}

const data: SearchData[] = [
  {
    id: '35',
    name: 'User List',
    url: '/apps/user/list',
    icon: 'ri-file-user-line',
    section: 'Apps'
  },
  {
    id: '36',
    name: 'User View',
    url: '/apps/user/view',
    icon: 'ri-file-list-2-line',
    section: 'Apps'
  },
  {
    id: '37',
    name: 'Roles',
    url: '/apps/roles',
    icon: 'ri-shield-user-line',
    section: 'Apps'
  },
  {
    id: '38',
    name: 'Permissions',
    url: '/apps/permissions',
    icon: 'ri-lock-unlock-line',
    section: 'Apps'
  },
  {
    id: '40',
    name: 'Account Settings',
    url: '/pages/account-settings',
    icon: 'ri-user-settings-line',
    section: 'Pages'
  },
  {
    id: '76',
    name: 'Menu Examples',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/menu-examples/overview`,
    icon: 'ri-menu-add-line',
    section: 'Others'
  },
  {
    id: '77',
    name: 'Typography',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/foundation/typography`,
    icon: 'ri-text',
    section: 'Foundation'
  },
  {
    id: '78',
    name: 'Colors',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/foundation/colors`,
    icon: 'ri-palette-line',
    section: 'Foundation'
  },
  {
    id: '79',
    name: 'Shadows',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/foundation/shadows`,
    icon: 'ri-shadow-line',
    section: 'Foundation'
  },
  {
    id: '80',
    name: 'Icons',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/foundation/icons`,
    icon: 'ri-remixicon-line',
    section: 'Foundation'
  },
  {
    id: '81',
    name: 'Accordion',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/accordion`,
    icon: 'ri-fullscreen-exit-line',
    section: 'Components'
  },
  {
    id: '82',
    name: 'Alerts',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/alerts`,
    icon: 'ri-alert-line',
    section: 'Components'
  },
  {
    id: '83',
    name: 'Avatars',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/avatars`,
    icon: 'ri-account-circle-line',
    section: 'Components'
  },
  {
    id: '84',
    name: 'Badges',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/badges`,
    icon: 'ri-notification-badge-line',
    section: 'Components'
  },
  {
    id: '85',
    name: 'Buttons',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/buttons`,
    icon: 'ri-download-2-line',
    section: 'Components'
  },
  {
    id: '86',
    name: 'Button Group',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/button-group`,
    icon: 'ri-file-copy-line',
    section: 'Components'
  },
  {
    id: '87',
    name: 'Chips',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/chips`,
    icon: 'ri-text-snippet',
    section: 'Components'
  },
  {
    id: '88',
    name: 'Dialogs',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/dialogs`,
    icon: 'ri-tv-2-line',
    section: 'Components'
  },
  {
    id: '89',
    name: 'List',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/list`,
    icon: 'ri-list-ordered',
    section: 'Components'
  },
  {
    id: '90',
    name: 'Menu',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/menu`,
    icon: 'ri-menu-line',
    section: 'Components'
  },
  {
    id: '91',
    name: 'Pagination',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/pagination`,
    icon: 'ri-skip-right-line',
    section: 'Components'
  },
  {
    id: '92',
    name: 'Progress',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/progress`,
    icon: 'ri-progress-3-line',
    section: 'Components'
  },
  {
    id: '93',
    name: 'Ratings',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/ratings`,
    icon: 'ri-star-line',
    section: 'Components'
  },
  {
    id: '94',
    name: 'Snackbar',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/snackbar`,
    icon: 'ri-message-3-line',
    section: 'Components'
  },
  {
    id: '95',
    name: 'Swiper',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/swiper`,
    icon: 'ri-slideshow-4-line',
    section: 'Components'
  },
  {
    id: '96',
    name: 'Tabs',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/tabs`,
    icon: 'ri-tv-2-line',
    section: 'Components'
  },
  {
    id: '97',
    name: 'Timeline',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/timeline`,
    icon: 'ri-timeline-view',
    section: 'Components'
  },
  {
    id: '98',
    name: 'Toasts',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/toasts`,
    icon: 'ri-notification-2-line',
    section: 'Components'
  },
  {
    id: '99',
    name: 'More Components',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components/more`,
    icon: 'ri-layout-grid-line',
    section: 'Components'
  },
  {
    id: '100',
    name: 'Text Field',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/form-elements/text-field`,
    icon: 'ri-input-field',
    section: 'Forms & Tables'
  },
  {
    id: '101',
    name: 'Select',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/form-elements/select`,
    icon: 'ri-list-check',
    section: 'Forms & Tables'
  },
  {
    id: '102',
    name: 'Checkbox',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/form-elements/checkbox`,
    icon: 'ri-checkbox-line',
    section: 'Forms & Tables'
  },
  {
    id: '103',
    name: 'Radio',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/form-elements/radio`,
    icon: 'ri-radio-button-line',
    section: 'Forms & Tables'
  },
  {
    id: '104',
    name: 'Custom Inputs',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/form-elements/custom-inputs`,
    icon: 'ri-list-radio',
    section: 'Forms & Tables'
  },
  {
    id: '105',
    name: 'Textarea',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/form-elements/textarea`,
    icon: 'ri-rectangle-line',
    section: 'Forms & Tables'
  },
  {
    id: '106',
    name: 'Autocomplete',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/form-elements/autocomplete`,
    icon: 'ri-list-check',
    section: 'Forms & Tables'
  },
  {
    id: '107',
    name: 'Date & Time Pickers',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/form-elements/pickers`,
    icon: 'ri-calendar-check-line',
    section: 'Forms & Tables'
  },
  {
    id: '108',
    name: 'Switch',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/form-elements/switch`,
    icon: 'ri-toggle-line',
    section: 'Forms & Tables'
  },
  {
    id: '109',
    name: 'File Uploader',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/form-elements/file-uploader`,
    icon: 'ri-file-upload-line',
    section: 'Forms & Tables'
  },
  {
    id: '110',
    name: 'Editor',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/form-elements/editor`,
    icon: 'ri-ai-generate',
    section: 'Forms & Tables'
  },
  {
    id: '111',
    name: 'Slider',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/form-elements/slider`,
    icon: 'ri-equalizer-2-line',
    section: 'Forms & Tables'
  },
  {
    id: '112',
    name: 'MUI Tables',
    url: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/mui-table`,
    icon: 'ri-table-2',
    section: 'Forms & Tables'
  }
]

export default data
