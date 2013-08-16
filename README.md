To create a plugin that allows a Joomla 3.x admin to restrict part of the
content article per user group. For instance I create a content article with
general public information, but also as part of that article I want to include
information for a specific registered user group, so when they have logged
in they see the full article content. This then prevents the need to create
two separate content articles for registered and non registered users.

The content to be hidden would be in the same article editor window but surrounded by say:
 
{restricted=Registered} NOTE that the group name is Case Sensitive

Only registered users see this content

{/restricted}
 
With the bit after the equals in the first brackets to correspond to the
single user group to allow access to this content
 
LIMITATIONS:
============

 1) Only allows for one secured block of text per article.
 2) Only allows a single group name to be controlled per block
 3) Group name MUST match all case sensitive name
 
 