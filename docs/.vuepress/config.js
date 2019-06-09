module.exports = {
  title: 'PHP Insights',
  description: 'The perfect starting point to analyze the code quality of your PHP projects',
  sidebar: true,
  themeConfig: {
    repo: 'nunomaduro/phpinsights',
    sidebar: [
      '/get-started',
      '/testimonial',
      '/configuration',
      {
        title: 'Insights',
        collapsable: false,
        children: [
          '/insights/',
        ]
      },
      '/contribute',
      '/support',
    ],
    algolia: {
      apiKey: 'cc2ada22e5f17c4068a51fea34db4abd',
      indexName: 'phpinsights'
    }
  },
}
