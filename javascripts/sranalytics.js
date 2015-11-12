(function(){
  __sranalyticsPluginVersion = sranalytics.version;
  __reach_config = {
    pid: sranalytics.pid,
    title: sranalytics.title,
    url: sranalytics.url,
    date: sranalytics.date,
    authors: sranalytics.authors,
    channels: sranalytics.channels,
    tags: sranalytics.tags
  };

  if(sranalytics.manual_scroll_depth === "1") {
    __reach_config.manual_scroll_depth = true;
  }
  var s = document.createElement('script');
  s.async = true;
  s.type = 'text/javascript';
  s.src = document.location.protocol + '//d8rk54i4mohrb.cloudfront.net/js/reach.js';
  (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(s);
})();
