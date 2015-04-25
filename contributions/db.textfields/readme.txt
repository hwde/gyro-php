Example for UrlPartialRestriction:

- require a http/https Url by COMPARE_STRING:

  new DBFieldTextUrlRestricted('url', null, DBField::NONE, 512, array(
      new UrlPartialRestriction(UrlPartialRestriction::COMPARE_STRING, 'scheme', array('http','https'), '%name is not a http(s) Url'),
  )),

- require a .de domain using COMPARE_REGEX:

  new DBFieldTextUrlRestricted('url', null, DBField::NONE, 512, array(
      new UrlPartialRestriction(UrlPartialRestriction::COMPARE_REGEX, 'host', '/\.de$/i', '%name is not .de domain'),
  )),

- checks the is no "password" parameter in the query string using COMPARE_REGEX:

  new UrlPartialRestriction(UrlPartialRestriction::COMPARE_CLOSURE, 'query', function($q) {
    return strpos($q, 'password') === false;
  }, '%name contains password parameter'),

