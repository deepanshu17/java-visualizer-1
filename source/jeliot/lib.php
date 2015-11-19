<?php  // $Id: lib.php,v 1.4 2006/08/28 16:41:20 mark-nielsen Exp $
/**
 * Library of functions and constants for module jeliot
 *
 * @author
 * @version $Id: lib.php,v 1.4 2006/08/28 16:41:20 mark-nielsen Exp $
 * @package jeliot
 **/


$jeliot_CONSTANT = 7;     /// for example

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will create a new instance and return the id number
 * of the new instance.
 *
 * @param object $instance An object from the form in mod.html
 * @return int The id of the newly inserted jeliot record
 **/
function jeliot_add_instance($jeliot) {
	global $DB;

    $jeliot->timemodified = time();

    # May have to add extra stuff in here #

    return $DB->insert_record("jeliot", $jeliot);
}

/**
 * Given an object containing all the necessary data,
 * (defined by the form in mod.html) this function
 * will update an existing instance with new data.
 *
 * @param object $instance An object from the form in mod.html
 * @return boolean Success/Fail
 **/
function jeliot_update_instance($jeliot) {
	global $DB;

    $jeliot->timemodified = time();
    $jeliot->id = $jeliot->instance;

    # May have to add extra stuff in here #

    return $DB->update_record("jeliot", $jeliot);
}

/**
 * Given an ID of an instance of this module,
 * this function will permanently delete the instance
 * and any data that depends on it.
 *
 * @param int $id Id of the module instance
 * @return boolean Success/Failure
 **/
function jeliot_delete_instance($id) {
	global $DB;

    if (! $jeliot = $DB->get_record("jeliot", array('id' => $id))) {
        return false;
    }

    $result = true;

    # Delete any dependent records here #
    if (! $DB->delete_records('jeliot', array('id' => $jeliot->id))) {
        $result = false;
    }
    if (!$jeliot_accesses =$DB->get_records ('jeliot_accesses',array('jeliotid' => $jeliot->id))) {
        return $result;
    } else if (!$DB->delete_records('jeliot_accesses',array('jeliotid' => $jeliot->id))) {
            $result = false;
    }
    return $result;
}

/**
 * Return a small object with summary information about what a
 * user has done with a given particular instance of this module
 * Used for user activity reports.
 * $return->time = the time they did it
 * $return->info = a short text description
 *
 * @return null
 * @todo Finish documenting this function
 **/
function jeliot_user_outline($course, $user, $mod, $jeliot) {
    global $CFG;
	global $DB;
	$no_of_times_taken=$DB->count_records('jeliot_accesses', 'userid', $user->id, 'jeliotid', $jeliot->id);
	$params['jeliotid'] = $jeliot->id;
	$params['userid'] = $user->id;
	if ($no_of_times_taken > 0)
	{
		$sql='SELECT MAX(accesses.timemodified) as lastime FROM {jeliot_accesses} AS accesses '.
			' WHERE accesses.jeliotid = :jeliotid AND accesses.userid = :userid';

		$lasttime=$DB->get_field_sql($sql);
		$return->time = $lasttime;
		$return->info = get_string('visualized_n_times', 'jeliot', $no_of_times_taken);

	} else
	{
		$return->time = time();
		$return->info = get_string('not_visualized', 'jeliot');
	}

    return $return;
}

/**
 * Print a detailed representation of what a user has done with
 * a given particular instance of this module, for user activity reports.
 *
 * @return boolean
 * @todo Finish documenting this function
 **/
function jeliot_user_complete($course, $user, $mod, $jeliot) {
        global $CFG;
		global $DB;
		$params['jeliotid'] = $jeliot->id;
		$params['userid'] = $user->id;
	    global $showall;
            $a->n=$DB->count_records('jeliot_accesses', 'userid', $user->id, 'jeliotid', $jeliot->id);
	    if ($a->n > 0){
	        $sql='SELECT MAX(accesses.timemodified) as lasttime, MIN(accesses.timemodified) as firsttime FROM {jeliot_accesses} AS accesses '.
	            ' WHERE accesses.jeliotid = :jeliotid AND accesses.userid = :userid';

	        $times=$DB->get_record_sql($sql);
	        $a->lasttime=userdate($times->lasttime);
	        $a->firsttime=userdate($times->firsttime);

	        print_string('visualized_n_times_most_recent_first', 'jeliot', $a);
	        return jeliot_print_table_of_accesses($jeliot, $user, $showall );

	    } else{
	        print_string('not_visualized', 'jeliot');
	        return;
	    }

}


/**
 * Given a course and a time, this module should find recent activity
 * that has occurred in jeliot activities and print it out.
 * Return true if there was output, or false is there was none.
 *
 * @uses $CFG
 * @return boolean
 * @todo Finish documenting this function
 **/
// function jeliot_print_recent_activity($course, $isteacher, $timestart) {
//         global $CFG;
// 		global $DB;
// 		$params['courseid'] = $course->id;
// 		$params['timestart'] = $timestart;
// 	    $sql='SELECT jeliot.id as id, COUNT(*) as n, jeliot.name as name, MAX(accesses.timemodified) as lasttime FROM {jeliot_accesses} AS accesses , {jeliot} AS jeliot '.
// 	    'WHERE accesses.jeliotid = jeliot.id  AND jeliot.course = :courseid'.' '.
// 	    'AND accesses.timemodified > :timestart GROUP BY jeliot.id';
// 	    //echo 'sql :'.$sql;
// 	    if ($records=$DB->get_records_sql($sql))
// 	    {
// 	        print '<a href="'.$CFG->wwwroot.'/mod/jeliot/index.php?id='.$course->id.'"><b><font size="2">'.get_string('modulenameplural','jeliot').' :</font></b></a><br />';
// 	        print '<font size="1">';
// 	        foreach ($records as $a)
// 	        {
// 	            print "<p>";
// 	            $a->lasttime=userdate($a->lasttime);
// 	               print '<a href="'.$CFG->wwwroot.'/mod/jeliot/view.php?a='.$a->id.'">'.$a->name.'</a> : <br />';
// 	            print_string('visualized_n_times_most_recent', 'jeliot', $a);
// 	            print "</p>\n";
// 	        }
// 	        print '</font>';
// 	        return true;
// 	    } else
// 	    {
// 	        return false;
//     }
// }

/**
 * Function to be run periodically according to the moodle cron
 * This function searches for things that need to be done, such
 * as sending out mail, toggling flags etc ...
 *
 * @uses $CFG
 * @return boolean
 * @todo Finish documenting this function
 **/
function jeliot_cron () {
    global $CFG;

    return true;
}

/**
 * Must return an array of grades for a given instance of this module,
 * indexed by user.  It also returns a maximum allowed grade.
 *
 * Example:
 *    $return->grades = array of grades;
 *    $return->maxgrade = maximum allowed grade;
 *
 *    return $return;
 *
 * @param int $jeliotid ID of an instance of this module
 * @return mixed Null or object with an array of grades and with the maximum grade
 **/
function jeliot_grades($jeliotid) {
   return NULL;
}

/**
 * Must return an array of user records (all data) who are participants
 * for a given instance of jeliot. Must include every user involved
 * in the instance, independient of his role (student, teacher, admin...)
 * See other modules as example.
 *
 * @param int $jeliotid ID of an instance of this module
 * @return mixed boolean/array of students
 **/
function jeliot_get_participants($jeliotid) {
	global $DB;
    $students = $DB->get_records_sql("SELECT u.*
	                                 FROM {user} u,
	                                      {jeliot_accesses} a
	                                 WHERE a.jeliotid = '$jeliotid' and
                                       u.id = a.userid");
}

/**
 * This function returns if a scale is being used by one jeliot
 * it it has support for grading and scales. Commented code should be
 * modified if necessary. See forum, glossary or journal modules
 * as reference.
 *
 * @param int $jeliotid ID of an instance of this module
 * @return mixed
 * @todo Finish documenting this function
 **/
function jeliot_scale_used ($jeliotid,$scaleid) {
	global $DB;
    $return = false;

    //$rec = $DB->get_record("jeliot","id","$jeliotid","scale","-$scaleid");
    //
    //if (!empty($rec)  && !empty($scaleid)) {
    //    $return = true;
    //}

    return $return;
}

//////////////////////////////////////////////////////////////////////////////////////
/// Any other jeliot functions go here.  Each of them must have a name that
/// starts with jeliot_
/* Creates the link to the JNLP file with the right parameters:
    codebase -> the location of the jnlp file (named WebStartJeliot.php) and the jar file (jeliot.jar)
    userID -> id of the user starting the webstart application
    sessionID -> content of the cookies required to access the files in the format name=Value;nameTest=value
    adapt->passes the adapt parameter
*/
function jeliot_create_JNLP_link($course, $jeliot  ){
   global $USER, $CFG, $DB;
   
   $sessionName = session_name();
   $sessionTestName= 'MoodleSession'.$CFG->sessioncookie;//sessionName."Test";
   $userID = $USER->username;
  
   $filenameabc = $DB->get_records_sql("SELECT u.*
	                                 FROM {files} u
	                                 WHERE u.itemid = '$jeliot->sourcefile' and
                                       u.filesize != 0");
    foreach ($filenameabc as $filenamea)
    {
    	$filename = $filenamea->filename;
    	$contextid = $filenamea->contextid;
    	$component = $filenamea->component;
    	$filearea = $filenamea->filearea;
    }

   $sourcefile=urlencode($CFG->wwwroot."/draftfile.php/".$contextid."/".$component."/".$filearea."/".$jeliot->sourcefile."/".$filename."?forcedownload=1");
   $sessionID = urlencode($sessionName. '=' . session_id());
   $codebase = urlencode($CFG->wwwroot."/mod/jeliot/");
   $parameters = "?codebase=".$codebase."&amp;sourcefile=".$sourcefile."&amp;userID=".$userID."&amp;sessionID=".$sessionID."&amp;questions=".$jeliot->questions;
   
   //TODO Add proper group selectio
   $link = "WebStartJeliot.php".$parameters;
 
   return $link;
}

function jeliot_return_sourcefile($course, $jeliot){
  global $USER, $CFG, $DB;

  $fs = get_file_storage();
   
   $sessionName = session_name();
   $sessionTestName= 'MoodleSession'.$CFG->sessioncookie;//sessionName."Test";
   $userID = $USER->username;
  
   $filenameabc = $DB->get_records_sql("SELECT u.*
                                   FROM {files} u
                                   WHERE u.itemid = '$jeliot->sourcefile' and
                                       u.filesize != 0");
    foreach ($filenameabc as $filenamea)
    {
      $filename = $filenamea->filename;
      $contextid = $filenamea->contextid;
      $component = $filenamea->component;
      $filearea = $filenamea->filearea;
    }

    $fileinfo = array(
      'component' => $component,     // usually = table name
    'filearea' => $filearea,     // usually = table name
    'itemid' => $jeliot->sourcefile,               // usually = ID of row in table
    'contextid' => $contextid, // ID of context
    'filepath' => '/',           // any path beginning and ending in /
    'filename' => $filename);

    $fileread = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'],
                      $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);

    $content = $fileread->get_content();

    return $content;
}

?>
