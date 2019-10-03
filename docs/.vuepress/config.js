module.exports = {
  title: 'PHP Insights',
  description: 'The perfect starting point to analyze the code quality of your PHP projects',
  sidebar: true,
  head: [
    ['link', { rel: 'icon', href: '/heart.png' }],
    ['link', { rel: 'icon', href: '/heart.svg', type: 'image/svg+xml' }],
  ],
  themeConfig: {
    repo: 'nunomaduro/phpinsights',
    sidebar: [
      '/get-started',
      '/testimonial',
      '/configuration',
      '/continuous-integration',
      {
        title: 'Insights',
        collapsable: false,
        children: [
          '/insights/',
          '/insights/code',
          '/insights/architecture',
          '/insights/complexity',
          '/insights/style',
        ]
      },
      '/contribute',
      '/support',
      '/changelog'
    ],
    nav: [
      { text: 'Changelog', link: '/changelog' },
    ],
    algolia: {
      apiKey: 'cc2ada22e5f17c4068a51fea34db4abd',
      indexName: 'phpinsights'
    }
  },
}
