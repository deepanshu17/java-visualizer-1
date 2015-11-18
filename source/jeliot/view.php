<?php  // $Id: view.php,v 1.6 2007/09/03 12:23:36 jamiesensei Exp $
/**
 * This page prints a particular instance of jeliot
 *
 * @author
 * @version $Id: view.php,v 1.6 2007/09/03 12:23:36 jamiesensei Exp $
 * @package jeliot
 **/

    require_once("../../config.php");
    require_once("lib.php");

    $id = optional_param('id', 0, PARAM_INT); // Course Module ID, or
    $a  = optional_param('a', 0, PARAM_INT);  // jeliot ID

    if ($id) {
        if (! $cm = $DB->get_record("course_modules", array('id' => $id))) {
            error("Course Module ID was incorrect");
        }

        if (! $course = $DB->get_record("course", array('id' => $cm->course))) {
            error("Course is misconfigured");
        }

        if (! $jeliot = $DB->get_record("jeliot", array('id' => $cm->instance))) {
            error("Course module is incorrect");
        }

    } else {
        if (! $jeliot = $DB->get_record("jeliot", array('id' => $a))) {
            error("Course module is incorrect");
        }
        if (! $course = $DB->get_record("course", array('id' => $jeliot->course))) {
            error("Course is misconfigured");
        }
        if (! $cm = get_coursemodule_from_instance("jeliot", $jeliot->id, $course->id)) {
            error("Course Module ID was incorrect");
        }
    }

	$params = array();
	$params['id'] = $id;
	$PAGE->set_url('/mod/jeliot/view.php', $params);

    require_login($course->id);

	$event = \mod_jeliot\event\course_module_viewed::create(array('objectid' => $jeliot->id, 'context' => context_module::instance($cm->id)));
	$event->trigger();

    //add_to_log($course->id, "jeliot", "view", "view.php?id=$cm->id", "$jeliot->id");

/// Print the page header
    $strjeliots = get_string("modulenameplural", "jeliot");
    $strjeliot  = get_string("modulename", "jeliot");

	$title = $course->shortname." : ".$jeliot->name;
	$PAGE->set_title($title);
	$PAGE->set_heading($course->fullname);

    if ($course->category) {
        $navigation = "<a href=\"../../course/view.php?id=$course->id\">$course->shortname</a> ->";
    } else {
        $navigation = '';
    }

    $linkCSS = "<link rel=\"stylesheet\" type=\"text/css\" href=\"jeliot.css\" />";
    $linkCSS = "<link rel=\"stylesheet\" type=\"text/css\" 
        href=\"http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css\">";

    echo $OUTPUT->header();
	echo '<center>'.$OUTPUT->heading($jeliot->name).'</center>';
    echo '<style> 
            	@import url("http://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css");            
	    	@import url(https://fonts.googleapis.com/css?family=Slabo+27px);

		div#jeliot_link{
		        //border : 3px solid red;
			margin: 0 auto;
			left: 20%;
			width: 900px;
            	}
		div#jeliot_description p{
			text-align:left;
		}
		div#jeliot_sourceCode{
		        font-size:0.9em;   
		} 
		.btn{
		        padding: 6px;
		        border-radius: 0;
		        background: yellow;
		        text-decoration:none;
		        text-color:red;
		}
		.btn-default{
		        color:red;
		}
		pre {
			width: 900px;                          /* specify width  */
			white-space: pre-wrap;                 /* CSS3 browsers  */
			white-space: -moz-pre-wrap !important; /* 1999+ Mozilla  */
			white-space: -pre-wrap;                /* Opera 4 thru 6 */
			white-space: -o-pre-wrap;              /* Opera 7 and up */
			word-wrap: break-word;                 /* IE 5.5+ and up */
			/* overflow-x: auto; */                /* Firefox 2 only */
			/* width: 99%; */		       /* only if needed */
		}

        </style>';
    echo '<script src="https://cdn.rawgit.com/google/code-prettify/master/loader/run_prettify.js"></script>';
    
/// Print the main part of the page
?>  

    <div id="jeliot_description">
      <p>
        <center><?php echo $jeliot->intro; ?></center>
      </p>
    </div>
    <div id="jeliot_link">  
	<!--<?php echo $course->id."**".$jeliot->sourcefile;?>-->
	
    <?php 
        $code = jeliot_return_sourcefile($course, $jeliot);
        echo "<pre class='prettyprint'>".$code."</pre>";
        $baseurl = "http://localhost/java_visualize/#mode=display&curInstr=0&code=";
        $baseurl = $baseurl.urlencode($code);

        echo '<center><a title="Visualize Online" target="_blank" 
                    href="'.$baseurl.'"><button>Visualize Online!</button></a>';
   
    ?>
	 <a title="Start Jeliot!" href="<?php echo jeliot_create_JNLP_link($course, $jeliot);?>">
         <button>Visualize in Jeliot</button></a></center>

<?php
/// Finish the page
    print_footer($course);
?>
