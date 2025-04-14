<?xml version="1.0" encoding="UTF-8"?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">
  <ShortName>{{ $title }}</ShortName>
  <Description>{{ $description }}</Description>
  <InputEncoding>{{ $encoding }}</InputEncoding>
  <LongName>{{ $longname }}</LongName>
  <Image width="16" height="16" type="image/x-icon">{{ $baseurl }}/favicon.ico</Image>
  <Url rel="results" type="text/html" method="get" template="{{ $baseurl }}/search?q={searchTerms}" />
</OpenSearchDescription>