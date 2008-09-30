<?php

/*========================================================================
*   Open eClass 2.1
*   E-learning and Course Management System
* ========================================================================
*  Copyright(c) 2003-2008  Greek Universities Network - GUnet
*  A full copyright notice can be read in "/info/copyright.txt".
*
*  Developers Group:	Costas Tsibanis <k.tsibanis@noc.uoa.gr>
*			Yannis Exidaridis <jexi@noc.uoa.gr>
*			Alexandros Diamantidis <adia@noc.uoa.gr>
*			Tilemachos Raptis <traptis@noc.uoa.gr>
*
*  For a full list of contributors, see "credits.txt".
*
*  Open eClass is an open platform distributed in the hope that it will
*  be useful (without any warranty), under the terms of the GNU (General
*  Public License) as published by the Free Software Foundation.
*  The full license can be read in "/info/license/license_gpl.txt".
*
*  Contact address: 	GUnet Asynchronous eLearning Group,
*  			Network Operations Center, University of Athens,
*  			Panepistimiopolis Ilissia, 15784, Athens, Greece
*  			eMail: info@openeclass.org
* =========================================================================*/

/**===========================================================================
	class.wikiaccesscontrol.php
	@last update: 15-05-2007 by Thanos Kyritsis
	@authors list: Thanos Kyritsis <atkyritsis@upnet.gr>
	               
	based on Claroline version 1.7.9 licensed under GPL
	      copyright (c) 2001, 2007 Universite catholique de Louvain (UCL)
	      
	      original file: class.wikiaccesscontrol Revision: 1.6.2.2
	      
	Claroline authors: Frederic Minne <zefredz@gmail.com>
==============================================================================        
    @Description: 

    @Comments:
 
    @todo: 
==============================================================================
*/

    /**
     * Wiki access control library
     * ACLs are of the form
     *  accessLevel_privilege => boolean
     */
    class WikiAccessControl
    {
        /**
         * Check if a given access level can request a given
         * privilege in a given access control list.
         * For example check if a group member is allowed to edit a page
         * @param array accessControlList access control list
         * @param string accessLevel access level
         * @param string privilege requested privilege
         * @return boolean true if pivilege is granted, false either
         */
        function checkAccess( $accessControlList, $accessLevel, $privilege )
        {
            $prefixList = WikiAccessControl::prefixList();
            $privilegeList = WikiAccessControl::privilegeList();
            
            if ( isset( $prefixList[$accessLevel] ) &&
                    isset( $privilegeList[$privilege] ) )
            {
                $accessKey = $prefixList[$accessLevel]
                    . $privilegeList[$privilege]
                    ;

                $accessControlFlag = isset( $accessControlList[$accessKey] )
                    ? $accessControlList[$accessKey]
                    : false
                    ;
            }
            else
            {
                $accessControlFlag = false;
            }
        
            if ( $accessControlFlag == true )
            {
                return true;
            }
            else
            {
                return false;
            }
        }
        
        /**
         * lists the prefixes associated with the access levels
         * @return array associative array of the form
         *      accessLevel => prefix
         */
        function prefixList()
        {
            static $prefixList = array(
                'course' => 'course_',
                'group' => 'group_',
                'other' => 'other_'
            );
            
            return $prefixList;
        }
        
        /**
         * lists the privileges
         * @return array associative array of the form
         *      privilege => name
         */
        function privilegeList()
        {
            static $privilegeList = array(
                'read' => 'read',
                'edit' => 'edit',
                'create' => 'create'
            );
            
            return $privilegeList;
        }
        
        /**
         * get default access control list for a course wiki
         * @return array default course access control list
         */
        function defaultCourseWikiACL()
        {
            static $defaultCourseWikiACL = array(
                'course_read' => true,
                'course_edit' => true,
                'course_create' => true,
                'group_read' => false,
                'group_edit' => false,
                'group_create' => false,
                'other_read' => true,
                'other_edit' => false,
                'other_create' => false
            );
            
            return $defaultCourseWikiACL;
        }
        
        /**
         * get empty access control list (ie with all entries
         * set to false)
         * @return array empty access control list
         */
        function emptyWikiACL()
        {
            static $emptyWikiACL = array(
                'course_read' => false,
                'course_edit' => false,
                'course_create' => false,
                'group_read' => false,
                'group_edit' => false,
                'group_create' => false,
                'other_read' => false,
                'other_edit' => false,
                'other_create' => false
            );

            return $emptyWikiACL;
        }
        
        /**
         * get default access control list for a group wiki
         * @return array default group access control list
         */
        function defaultGroupWikiACL()
        {
            static $defaultGroupWikiACL = array(
                'course_read' => true,
                'course_edit' => false,
                'course_create' => false,
                'group_read' => true,
                'group_edit' => true,
                'group_create' => true,
                'other_read' => false,
                'other_edit' => false,
                'other_create' => false
            );

            return $defaultGroupWikiACL;
        }
        
        /**
         * check a given access control list to see wether or not a given
         * access level has got read privilege
         * @return boolean true if read privilege is granted, false either
         */
        function isAllowedToReadPage( $accessControlList, $accessLevel )
        {
            $privilege = 'read';
            return WikiAccessControl::checkAccess( $accessControlList, $accessLevel, $privilege );
        }
        
        /**
         * check a given access control list to see wether or not a given
         * access level has got edit privilege
         * @return boolean true if edit privilege is granted, false either
         */
        function isAllowedToEditPage( $accessControlList, $accessLevel )
        {
            $privilege = 'edit';
            return WikiAccessControl::checkAccess( $accessControlList, $accessLevel, $privilege );
        }
        
        /**
         * check a given access control list to see wether or not a given
         * access level has got create privilege
         * @return boolean true if create privilege is granted, false either
         */
        function isAllowedToCreatePage( $accessControlList, $accessLevel )
        {
            $privilege = 'create';
            return WikiAccessControl::checkAccess( $accessControlList, $accessLevel, $privilege );
        }
        
        /**
         * grant the given privilege to the given access level in the given access
         * control list
         * @param array accessControlList access control list
         * @param string accessLevel access level
         * @param string privilege privilege to grant
         * @return boolean true on success, false if accessLevel or
         *      privilege are not valid
         */
        function grantPrivilegeToAccessLevel( &$accessControlList, $accessLevel, $privilege )
        {
            $prefixList = WikiAccessControl::prefixList();
            $privilegeList = WikiAccessControl::privilegeList();

            if ( isset( $prefixList[$accessLevel] ) && isset( $privilegeList[$privilege] ) )
            {
                $key = $prefixList[$accessLevel] . $privilegeList[$privilege];
                $accessControlList[$key] = true;
                return true;
            }
            else
            {
                return false;
            }
        }
        
        /**
         * remove the given privilege from the given access level in the given access
         * control list
         * @param array accessControlList access control list
         * @param string accessLevel access level
         * @param string privilege privilege to remove
         * @return boolean true on success, false if accessLevel or
         *      privilege are not valid
         */
        function removePrivilegeFromAccessLevel( &$accessControlList, $accessLevel, $privilege )
        {
            $prefixList = WikiAccessControl::prefixList();
            $privilegeList = WikiAccessControl::privilegeList();

            if ( isset( $prefixList[$accessLevel] ) && isset( $privilegeList[$privilege] ) )
            {
                $key = $prefixList[$accessLevel] . $privilegeList[$privilege];
                $accessControlList[$key] = false;
                return true;
            }
            else
            {
                return false;
            }
        }
        
        /**
         * grant the read given privilege to the given access level in the given access
         * control list
         * @param array accessControlList access control list
         * @param string accessLevel access level
         * @return boolean true on success, false if accessLevel or
         *      privilege are not valid
         */
        function grantReadPrivilegeToAccessLevel( &$accessControlList, $accessLevel )
        {
            $privilege = 'read';

            return WikiAccessControl::grantPrivilegeToAccessLevel(
                $accessControlList
                , $accessLevel
                , $privilege
                );
        }
        
        /**
         * grant the edit given privilege to the given access level in the given access
         * control list
         * @param array accessControlList access control list
         * @param string accessLevel access level
         * @return boolean true on success, false if accessLevel or
         *      privilege are not valid
         */
        function grantEditPrivilegeToAccessLevel( &$accessControlList, $accessLevel )
        {
            $privilege = 'edit';
            
            return WikiAccessControl::grantPrivilegeToAccessLevel(
                $accessControlList
                , $accessLevel
                , $privilege
                );
        }
        
        /**
         * grant the create given privilege to the given access level in the given access
         * control list
         * @param array accessControlList access control list
         * @param string accessLevel access level
         * @return boolean true on success, false if accessLevel or
         *      privilege are not valid
         */
        function grantCreatePrivilegeToAccessLevel( &$accessControlList, $accessLevel )
        {
            $privilege = 'create';

            return WikiAccessControl::grantPrivilegeToAccessLevel(
                $accessControlList
                , $accessLevel
                , $privilege
                );
        }
        
        /**
         * remove the read privilege from the given access level in the given access
         * control list
         * @param array accessControlList access control list
         * @param string accessLevel access level
         * @return boolean true on success, false if accessLevel or
         *      privilege are not valid
         */
        function removeReadPrivilegeFromAccessLevel( &$accessControlList, $accessLevel )
        {
            $privilege = 'read';

            return WikiAccessControl::removePrivilegeFromAccessLevel(
                $accessControlList
                , $accessLevel
                , $privilege
                );
        }

        /**
         * remove the edit privilege from the given access level in the given access
         * control list
         * @param array accessControlList access control list
         * @param string accessLevel access level
         * @return boolean true on success, false if accessLevel or
         *      privilege are not valid
         */
        function removeEditPrivilegeFromAccessLevel( &$accessControlList, $accessLevel )
        {
            $privilege = 'edit';

            return WikiAccessControl::removePrivilegeFromAccessLevel(
                $accessControlList
                , $accessLevel
                , $privilege
                );
        }

        /**
         * remove the create privilege from the given access level in the given access
         * control list
         * @param array accessControlList access control list
         * @param string accessLevel access level
         * @return boolean true on success, false if accessLevel or
         *      privilege are not valid
         */
        function removeCreatePrivilegeFromAccessLevel( &$accessControlList, $accessLevel )
        {
            $privilege = 'create';

            return WikiAccessControl::removePrivilegeFromAccessLevel(
                $accessControlList
                , $accessLevel
                , $privilege
                );
        }
        
        /**
         * Export access control list to a string
         * @param array accessControlList access controllist
         * @param boolean echoExport print the exported value
         *      if set to true (default true)
         * @return string string representation of the access control list
         */
        function exportACL( $accessControlList, $echoExport = true )
        {
            $export = "<pre>\n";
            $prefixList = WikiAccessControl::prefixList();
            $privilegeList = WikiAccessControl::privilegeList();
            
            foreach ( $prefixList as $accessLevel => $prefix )
            {
                $export .= $accessLevel . ':';
                
                foreach ( $privilegeList as $privilege )
                {
                    $aclKey = $prefix . $privilege;
                    
                    $boolValue = ( $accessControlList[$aclKey] == true ) ? 'true' : 'false';
                    $export .= $privilege . '('.$boolValue.')';
                }
                
                $export .= "<br />\n";
            }
            
            $export .= "</pre>\n";
            
            if ( $echoExport == true )
            {
                echo $export;
            }
            
            return $export;
        }
    }
?>
