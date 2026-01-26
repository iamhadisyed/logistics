// Type Imports
import type { ChildrenType } from '@core/types'
import type { Locale } from '@configs/i18n'

const Layout = async (props: ChildrenType & { params: Promise<{ lang: Locale }> }) => {
  const { children } = props

  // This layout just passes through children since the parent (dashboard) layout
  // already provides all the navigation, header, footer, etc.
  return <>{children}</>
}

export default Layout
