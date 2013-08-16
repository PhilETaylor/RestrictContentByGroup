<?php
/**
 * @author      Phil Taylor <phil@phil-taylor.com>
 * @link        www.phil-taylor.com
 * @link        github
 * @copyright   Copyright (C) 2013 Blue Flame IT Ltd, Inc. All rights reserved.
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 *
 *
 * To create a plugin that allows a Joomla 3.x admin to restrict part of the
 * content article per user group. For instance I create a content article with
 * general public information, but also as part of that article I want to include
 * information for a specific registered user group, so when they have logged
 * in they see the full article content. This then prevents the need to create
 * two separate content articles for registered and non registered users.
 *
 * The content to be hidden would be in the same article editor window but surrounded by say:
 *
 * {restricted=Registered} NOTE that the group name is Case Sensitive
 *
 * Only registered users see this content
 *
 * {/restricted}
 *
 * With the bit after the equals in the first brackets to correspond to the
 * single user group to allow access to this content
 *
 * LIMITATIONS:
 * 1) Only allows for one secured block of text per article.
 * 2) Only allows a single group name to be controlled per block
 * 3) Group name MUST match all case sensitive name
 *
 */

defined('_JEXEC') or die;

class PlgContentRestrictor extends JPlugin
{
    /**
     * @var array The group levels in Joomla
     */
    private $_groups;

    /**
     * Plugin that adds a pagebreak into the text and truncates text at that point
     *
     * @param   string  $context  The context of the content being passed to the plugin.
     * @param   object  &$row     The article object.  Note $article->text is also available
     * @param   mixed   &$params  The article params
     * @param   integer $page     The 'page' number
     *
     * @return  void  Always returns void
     */
    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        // Only process items with a restriction
        if (!strpos($row->text, '{/restricted}')) return;

        /* @var $my JUser Get the currently logged in user */
        $my = JFactory::getUser();

        // Get the restricted to group
        preg_match_all('/{restricted=(.*)}/i', $row->text, $matches);

        // get the name of the group after the = sign
        $groupRestrictedTo = ($matches[1][0]);

        // Convert group name to group id
        $groupIdRestrictedTo = $this->getGroupId($groupRestrictedTo);

        // Am I allowed to see this content?
        if (in_array($groupIdRestrictedTo, $my->getAuthorisedGroups())) {

            // ok so I'm allowed, so dont nuke the content, only nuke the placeholders.
            $row->text = str_replace(array('{/restricted}','{restricted=' . $groupRestrictedTo . '}'), '', $row->text);

        } else {

            // I'm NOT allowed so nuke the whole block of text and the placeholders
            $row->text = preg_replace('/{restricted=Registered}(.*){\/restricted}/is', '', $row->text);
        }
    }

    /**
     * I get the group names and ids from the user groups table.
     * Apparently there is no API to do this, one has to access to the db directly!
     * Grrr...
     *
     * @return int The Group ID number
     */
    private function getGroupId($groupName)
    {

        $db = JFactory::getDbo();

        $query = $db->getQuery(TRUE)
                 ->select($db->quoteName('id') . ', ' . $db->quoteName('title'))
                 ->from($db->quoteName('#__usergroups'))
                 ->where($db->quoteName('title') . ' = ' . $db->quote($groupName));

        $db->setQuery($query);

        return $db->loadResult();

    }
}