// Next Imports
import { useParams } from 'next/navigation'
import { useEffect, useState } from 'react'

// MUI Imports
import { useTheme } from '@mui/material/styles'

// Third-party Imports
import PerfectScrollbar from 'react-perfect-scrollbar'
import { useSession } from 'next-auth/react'

// Type Imports
import type { getDictionary } from '@/utils/getDictionary'
import type { VerticalMenuContextProps } from '@menu/components/vertical-menu/Menu'
import type { VerticalMenuDataType } from '@/types/menuTypes'

// Component Imports
import { Menu } from '@menu/vertical-menu'
import { GenerateVerticalMenu } from '@components/GenerateMenu'

// Hook Imports
import useVerticalNav from '@menu/hooks/useVerticalNav'

// Styled Component Imports
import StyledVerticalNavExpandIcon from '@menu/styles/vertical/StyledVerticalNavExpandIcon'

// Style Imports
import menuItemStyles from '@core/styles/vertical/menuItemStyles'
import menuSectionStyles from '@core/styles/vertical/menuSectionStyles'

type RenderExpandIconProps = {
  open?: boolean
  transitionDuration?: VerticalMenuContextProps['transitionDuration']
}

type Props = {
  dictionary: Awaited<ReturnType<typeof getDictionary>>
  scrollMenu: (container: any, isPerfectScrollbar: boolean) => void
}

const RenderExpandIcon = ({ open, transitionDuration }: RenderExpandIconProps) => (
  <StyledVerticalNavExpandIcon open={open} transitionDuration={transitionDuration}>
    <i className='ri-arrow-right-s-line' />
  </StyledVerticalNavExpandIcon>
)

const resolveLabel = (key: string, dict: any) => {
  if (!key) return '';
  return key.split('.').reduce((acc, part) => acc && acc[part], dict) || key;
}

const mapData = (items: any[], dict: any): VerticalMenuDataType[] => {
  return items.map(item => {
    // Determine type based on children and file_name
    // If exact mapping is needed from API to VerticalMenuDataType:
    // API returns: { label, href, icon, children, ... }

    // Recursive mapping
    const children = item.children ? mapData(item.children, dict) : undefined;

    const label = resolveLabel(item.label, dict);

    return {
      ...item,
      label,
      children
    } as VerticalMenuDataType;
  })
}

const VerticalMenu = ({ dictionary, scrollMenu }: Props) => {
  // Hooks
  const theme = useTheme()
  const verticalNavOptions = useVerticalNav()
  const params = useParams()
  const { data: session } = useSession()
  const [menuData, setMenuData] = useState<VerticalMenuDataType[]>([])
  const [isMounted, setIsMounted] = useState(false)

  // Vars
  const { isBreakpointReached, transitionDuration } = verticalNavOptions
  const { lang: locale } = params

  const ScrollWrapper = isBreakpointReached ? 'div' : PerfectScrollbar

  useEffect(() => {
    setIsMounted(true);
    // @ts-ignore
    const token = session?.user?.accessToken;
    if (!token) return;

    // Use environment variable or fallback to localhost
    // Ensure we don't double-stack 'api' if the env var has it, but standard practice is env var = host.
    // The error "The route api/api/sidebar" suggests the request was made to `.../api/api/sidebar`.
    // This implies `apiUrl` might be pointing to backend/api or the construct is wrong.
    // However, typical Laravel API is at /api.
    // Let's check if NEXT_PUBLIC_API_URL includes /api. 
    // If we can't see env, we should be safe.
    // BUT, the error message clearly says `api/api/sidebar` (relative?) or `.../api/api/sidebar`.
    // I will try to strictly use the host and append /api/sidebar.

    // Simplest fix:
    const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8000';
    const baseUrl = apiUrl.endsWith('/api') ? apiUrl.slice(0, -4) : apiUrl;

    fetch(`${baseUrl}/api/sidebar`, {
      headers: {
        'Authorization': `Bearer ${token}`,
        'Accept': 'application/json'
      }
    })
      .then(async res => {
        if (!res.ok) {
          const text = await res.text();
          console.error('Sidebar API Error:', res.status, text);
          throw new Error(`Failed to fetch menu: ${res.status}`);
        }
        return res.json();
      })
      .then(data => {
        const mapped = mapData(data, dictionary);
        setMenuData(mapped);
      })
      .catch(err => console.error(err));

  }, [session, dictionary]);

  if (!isMounted) {
    return null;
  }


  return (
    // eslint-disable-next-line lines-around-comment
    /* Custom scrollbar instead of browser scroll, remove if you want browser scroll only */
    <ScrollWrapper
      {...(isBreakpointReached
        ? {
          className: 'bs-full overflow-y-auto overflow-x-hidden',
          onScroll: container => scrollMenu(container, false)
        }
        : {
          options: { wheelPropagation: false, suppressScrollX: true },
          onScrollY: container => scrollMenu(container, true)
        })}
    >
      {/* Vertical Menu */}
      <Menu
        popoutMenuOffset={{ mainAxis: 17 }}
        menuItemStyles={menuItemStyles(verticalNavOptions, theme)}
        renderExpandIcon={({ open }) => <RenderExpandIcon open={open} transitionDuration={transitionDuration} />}
        renderExpandedMenuItemIcon={{ icon: <i className='ri-circle-fill' /> }}
        menuSectionStyles={menuSectionStyles(verticalNavOptions, theme)}
      >
        <GenerateVerticalMenu menuData={menuData} />
      </Menu>
    </ScrollWrapper>
  )
}

export default VerticalMenu
