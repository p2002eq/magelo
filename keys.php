<?php

/***************************************************************************

 *

 *   This program is free software; you can redistribute it and/or modify

 *   it under the terms of the GNU General Public License as published by

 *   the Free Software Foundation; either version 2 of the License, or

 *   (at your option) any later version.

 *

 *   Portions of this program are derived from publicly licensed software

 *   projects including, but not limited to phpBB, Magelo Clone,

 *   EQEmulator, EQEditor, and Allakhazam Clone.

 *

 *                                  Author:

 *                           Maudigan(Airwalking)

 *

 ***************************************************************************/









define('INCHARBROWSER', true);

include_once("include/config.php");

include_once("include/profile.php");

include_once("include/global.php");

include_once("include/language.php");

include_once("include/functions.php");



//if character name isnt provided post error message and exit

if(!$_GET['char']) message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_CHAR']);

else $char = $_GET['char'];





//security against sql injection

if (!IsAlphaSpace($char)) message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NAME_ALPHA']);





// pull the characters ID and profile from the database

$results = mysql_query("SELECT id from character_ where name='$char' limit 1;");

if (!($row = mysql_fetch_array($results))) message_die($language['MESSAGE_ERROR'],$language['MESSAGE_NO_FIND']);

$charID = $row['id'];

$char = new profile;

$char->LoadProfile($charID);

$name     = $char->GetValue('name');

$mypermission = GetPermissions($char->GetValue('gm'), $char->GetValue('anon'), $char->char_id());



//block view if user level doesnt have permission

if ($mypermission['keys']) message_die($language['MESSAGE_ERROR'],$language['MESSAGE_ITEM_NO_VIEW']);



//=============================

//grab the keys the user has

//=============================

$results = mysql_query("SELECT k.item_id, i.Name AS 'key' FROM keyring AS k LEFT JOIN items AS i ON i.id = k.item_id WHERE char_id =  $charID ORDER BY i.Name;");

if (!mysql_num_rows($results)) message_die($language['KEYS_KEY']." - ".$name,$language['MESSAGE_NO_KEYS']);



//drop page

$d_title = " - ".$name.$language['PAGE_TITLES_KEYS'];

include("include/header.php");



//build body template

$template->set_filenames(array(

        'keys' => 'keys_body.tpl')

);



$template->assign_vars(array(

        'NAME' => $name,



        'L_KEY' => $language['KEYS_KEY'],

        'L_KEYS' => $language['BUTTON_KEYS'],

        'L_AAS' => $language['BUTTON_AAS'],

        'L_FLAGS' => $language['BUTTON_FLAGS'],

        'L_SKILLS' => $language['BUTTON_SKILLS'],

        'L_CORPSE' => $language['BUTTON_CORPSE'],

        'L_FACTION' => $language['BUTTON_FACTION'],

        'L_BOOKMARK' => $language['BUTTON_BOOKMARK'],

        'L_INVENTORY' => $language['BUTTON_INVENTORY'],

        'L_CHARMOVE' => $language['BUTTON_CHARMOVE'],

        'L_DONE' => $language['BUTTON_DONE'])

);





while ($row = mysql_fetch_array($results)) {

    $template->assign_block_vars("keys", array(

            'KEY' => $row['key'],

            'ITEM_ID' => $row["item_id"])

    );

}





$template->pparse('keys');



$template->destroy;



include("include/footer.php");





?>