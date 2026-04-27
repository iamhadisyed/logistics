import type { NextConfig } from 'next'

const isStaticExport = process.env.NEXT_STATIC_EXPORT === 'true'

const nextConfig: NextConfig = {
  basePath: process.env.BASEPATH,
  ...(isStaticExport
    ? {
        output: 'export',
        eslint: {
          ignoreDuringBuilds: true
        },
        typescript: {
          ignoreBuildErrors: true
        },
        images: {
          unoptimized: true
        },
        trailingSlash: true
      }
    : {}),
  redirects: async () => {
    return [
      {
        source: '/',
        destination: '/en/dashboards/crm',
        permanent: true,
        locale: false
      },
      {
        source: '/:lang(en|fr|ar)',
        destination: '/:lang/dashboards/crm',
        permanent: true,
        locale: false
      },
      {
        source: '/((?!(?:en|fr|ar|front-pages|favicon.ico)\\b)):path',
        destination: '/en/:path',
        permanent: true,
        locale: false
      }
    ]
  }
}

export default nextConfig
