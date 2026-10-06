// Type Imports
import type { HorizontalMenuDataType } from '@/types/menuTypes'
import type { getDictionary } from '@/utils/getDictionary'

const horizontalMenuData = (dictionary: Awaited<ReturnType<typeof getDictionary>>): HorizontalMenuDataType[] => [
  {
    label: dictionary['navigation'].dashboards,
    icon: 'ri-home-smile-line',
    href: '/dashboard'
  },
  {
    label: dictionary['navigation'].apps,
    icon: 'ri-mail-open-line',
    children: [
      {
        label: 'Carriers',
        icon: 'ri-truck-line',
        children: [
          {
            label: 'List',
            href: '/carriers'
          },
          {
            label: 'Create',
            href: '/carriers/create'
          }
        ]
      },
      {
        label: dictionary['navigation'].user,
        icon: 'ri-user-line',
        children: [
          {
            label: dictionary['navigation'].list,
            href: '/apps/user/list'
          },
          {
            label: dictionary['navigation'].view,
            href: '/apps/user/view'
          }
        ]
      },
      {
        label: dictionary['navigation'].rolesPermissions,
        icon: 'ri-lock-line',
        children: [
          {
            label: dictionary['navigation'].roles,
            href: '/apps/roles'
          },
          {
            label: dictionary['navigation'].permissions,
            href: '/apps/permissions'
          }
        ]
      }
    ]
  },
  {
    label: dictionary['navigation'].pages,
    icon: 'ri-file-list-2-line',
    children: [
      {
        label: dictionary['navigation'].accountSettings,
        icon: 'ri-user-settings-line',
        href: '/pages/account-settings'
      }
    ]
  },
  {
    label: dictionary['navigation'].formsAndTables,
    icon: 'ri-pages-line',
    children: [
      {
        label: dictionary['navigation'].formELements,
        icon: 'ri-radio-button-line',
        suffix: <i className='ri-external-link-line text-xl' />,
        href: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/form-elements`,
        target: '_blank'
      },
      {
        label: dictionary['navigation'].muiTables,
        icon: 'ri-table-2',
        href: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/mui-table`,
        suffix: <i className='ri-external-link-line text-xl' />,
        target: '_blank'
      }
    ]
  },
  {
    label: dictionary['navigation'].others,
    icon: 'ri-more-line',
    children: [
      {
        label: dictionary['navigation'].foundation,
        icon: 'ri-pantone-line',
        href: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/foundation`,
        suffix: <i className='ri-external-link-line text-xl' />,
        target: '_blank'
      },
      {
        label: dictionary['navigation'].components,
        icon: 'ri-toggle-line',
        href: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/user-interface/components`,
        suffix: <i className='ri-external-link-line text-xl' />,
        target: '_blank'
      },
      {
        label: dictionary['navigation'].menuExamples,
        icon: 'ri-menu-search-line',
        href: `${process.env.NEXT_PUBLIC_DOCS_URL}/docs/menu-examples/overview`,
        suffix: <i className='ri-external-link-line text-xl' />,
        target: '_blank'
      },
      {
        label: dictionary['navigation'].raiseSupport,
        icon: 'ri-lifebuoy-line',
        href: 'https://pixinvent.ticksy.com',
        suffix: <i className='ri-external-link-line text-xl' />,
        target: '_blank'
      },
      {
        label: dictionary['navigation'].documentation,
        icon: 'ri-book-line',
        href: 'https://demos.pixinvent.com/materialize-nextjs-admin-template/documentation',
        suffix: <i className='ri-external-link-line text-xl' />,
        target: '_blank'
      }
    ]
  }
]

export default horizontalMenuData
