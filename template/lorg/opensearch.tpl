<?xml version="1.0" encoding="utf-8"?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/">
  <ShortName>{{ $title }}</ShortName>
  <Description>{{ $description }}</Description>
  <InputEncoding>{{ $encoding }}</InputEncoding>
  <LongName>{{ $longname }}</LongName>
  <Url rel="results" type="text/html" method="get" template="{{ $baseurl }}/search?q={searchTerms}" />
  <Url type="application/opensearchdescription+xml" rel="self" template="/opensearch.xml?method=GET" />
</OpenSearchDescription>