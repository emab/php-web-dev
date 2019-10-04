<?php
/**
 * Contains the definition of the Context class
 *
 * @author Lindsay Marshall <lindsay.marshall@ncl.ac.uk>
 * @author Eddy Brown <e.a.m.brown@ncl.ac.uk>
 * @copyright 2012-2016 Newcastle University
 */
/**
 * A class that stores various useful pieces of data for access throughout the rest of the system.
 * Also contains many methods used to power the website
 */
    class Context
    {
        use Singleton;
/**
 * The name of the authentication token field.
 */
	const TOKEN 	= 'X-APPNAME-TOKEN';
/**
 * The key used to encode the token validation
 */
	const KEY	= 'Some string of text.....';
/**
 * Value indicating to generate a 400 output from load function when id does not exist
 */
        const R400      = 0;
/**
 * Value indicating to generate a NULL return from load function when id does not exist
 */
        const RNULL     = 1;
/**
 * Value indicating to throw an error from load function when id does not exist
 */
        const RTHROW    = 2;
/**
 * @var object		NULL or an object decribing the current logged in User
 */
        private $luser	= NULL;		# Current user bean if we have logins....
/**
 * @var integer		Counter used for generating unique ids
 */
        private $idgen = 0;			# used for generating unique ids
/**
 * @var string		The first component of the current URL
 */
        private $reqaction	= 'home';	# the first segment of	the URL
/**
 * @var array		The rest of the current URL exploded at /
 */
        private $reqrest	= [];		# the rest of the URL
/**
 * @var boolean		True if authenticated by token
 */
	private $tokauth	= FALSE;
/*
 ***************************************
 * URL and REST support functions
 ***************************************
 */
/**
 * Return the main action part of the URL as set by .htaccess
 *
 * @return string
 */
        public function action()
        {
            return $this->reqaction;
        }
/**
 * Return the part of the URL after the main action as set by .htaccess
 *
 * See setup() below for how the URL is processed to create the result array.
 *
 * Note that if there is nothing after the action in the URL this function returns
 * an array with a single element containing an empty string.
 *
 * @return array
 */
        public function rest()
        {
            return $this->reqrest;
        }
/**
 ***************************************
 * User related functions
 ***************************************
 */
/**
 * Return the current logged in user if any
 *
 * @return object
 */
        public function user()
        {
            return $this->luser;
        }
/**
 * Get all the Theme beans
 *
 * @return array
 */
        public function themes()
        {
            return R::findAll('theme', 'order by name');
        }
/**
 * Get all the user choice beans
 *
 * @return array
 */
        public function userChoices()
        {
            return R::findAll('choice');
        }
/**
 * Get all the file uploads detail beans
 *
 * @return array
 */
        public function fileInfo()
        {
            return R::findAll('uploaddetail');
        }
/**
 * Get all the final theme choice beans
 *
 * @return array
 */
        public function finalChoices()
        {
            return R::findAll('finalchoice');
        }
/**
 * Return TRUE if the user in the parameter has been allocated a theme
 *
 * @param string    $userid
 *
 * @return boolean
 */
        public function hasFinalChoice($userid)
        {
            return is_object(R::findOne('finalchoice', 'user_id = ?', [$userid]));
        }
/**
 * Return TRUE if the user in the parameter has been allocated a supervisor
 *
 * @param string    $user
 *
 * @return boolean
 */
        public function hasGotSupervisor($userid)
        {
            return is_object(R::findOne('finalchoice', 'user_id=? AND supervisor_id IS NOT NULL', [$userid]));
        }
/**
 * Return TRUE if the user in the parameter has chosen their themes
 *
 * @param string    $userid
 *
 * @return boolean
 */
        public function hasMadeChoice($userid)
        {
            return is_object(R::findOne('choice', 'user_id = ?', [$userid]));
        }
/**
 * Return the name of the theme given its id
 *
 * @param string    $themeid
 *
 * @return string
 */
        public function getThemeName($themeid)
        {
            $theme = R::findOne('theme', 'id = ?', [$themeid]);
            return $theme->name;
        }
/**
 * Return the id of a theme given its name
 *
 * @param string    $themename
 *
 * @return int
 */
        public function getThemeId($themename)
        {
            $theme = R::findOne('theme', 'name = ?', [$themename]);
            return $theme->id;
        }
/**
 * Return any beans that correspond to a member of staff being associated with a theme
 *
 * @param string    $userid
 *
 * @return array
 */
        public function themeStaff($userid)
        {
            return R::findAll('themestaff', 'user_id = ?', [$userid]);
        }
/**
 * Return a users login given their id
 *
 * @param string    $userid
 *
 * @return string
 */
        public function getUserLogin($userid)
        {
            $user = R::findOne('user', 'id = ?', [$userid]);
            return $user->login;
        }
/**
 * Return a users email given their id
 *
 * @param string    $userid
 *
 * @return string
 */
        public function getUserEmail($userid)
        {
            $user = R::findOne('user', 'id = ?', [$userid]);
            return $user->email;
        }
/**
 * Return a file path given its id
 *
 * @param string    $fileid
 *
 * @return string
 */
        public function getFilePath($fileid)
        {
            $file = R::findOne('upload', 'id = ?', [$fileid]);
            return $file->fname;
        }
/**
 * Return a file name given its id
 *
 * @param string    $fileid
 *
 * @return string
 */
        public function getFileName($fileid)
        {
            $file = R::findOne('upload', 'id = ?', [$fileid]);
            return $file->filename;
        }
/**
 * Return TRUE if a the staff user is associated with a theme
 *
 * @param string    $userid
 * @param string    $themeid
 *
 * @return boolean
 */
        public function checkThemeStaff($userid, $themeid)
        {
            return is_object(R::findOne('themestaff', 'user_id = ? AND theme_id = ?', [$userid, $themeid]));
        }
/**
 * Return the number of users who need to have their theme finalised
 *
 * @return int
 */
        public function usersNeedChoice()
        {
            $madechoice = (R::count('choice'))/2;
            $choicedecided = R::count('finalchoice');
            return $madechoice - $choicedecided;
        }
/**
 * Return a the number of users who need a supervisor for a given theme
 *
 * @param string    $themeid
 *
 * @return int
 */
        public function usersNeedSupervisor($themeid)
        {
            return R::count('finalchoice', 'theme_id = ? AND supervisor_id IS NULL', [$themeid]);
        }
/**
 * Return the theme id of the users final choice of theme
 *
 * @param string    $userid
 *
 * @return int
 */
        public function getUserTheme($userid)
        {
            $theme = R::findOne('finalchoice', 'user_id = ?', [$userid]);
            return $theme->theme_id;
        }
/**
 * Return all files that have been uploaded
 *
 * @return array
 */
        public function getFiles()
        {
            return R::findAll('upload');
        }
/**
 * Return TRUE if the user in the parameter is the same as the current user
 *
 * @param object    $user
 *
 * @return boolean
 */
        public function sameuser($user)
        {
            return $this->hasuser() && $this->user()->equals($user);
        }
/**
 * Do we have a logged in user?
 *
 * @return boolean
 */
        public function hasuser()
        {
            return is_object($this->luser);
        }
/**
 * Do we have a logged in admin user?
 *
 * @return boolean
 */
        public function hasadmin()
        {
            return $this->hasuser() && $this->user()->isadmin();
        }

        public function getRoles()
        {
            return R::findAll('role');
        }

/**
 * Do we have a logged in developer user?
 *
 * @return boolean
 */
        public function hasdeveloper()
        {
            return $this->hasuser() && $this->user()->isdeveloper();
        }
/**
 * Find out if this was validated using a token, if so, it is coming from a device not a browser
 *
 * @return boolean
 */
	public function hastoken()
	{
	    return $this->tokauth;
	}
/**
 * Check for logged in and 403 if not
 */
        public function mustbeuser()
        {
            if (!$this->hasuser())
            {
                $this->web()->noaccess();
            }
        }
/**
 * Check for logged in and return false if not
 */
        public function isnotloggedin()
        {
            if (!$this->hasuser())
            {
                return TRUE;
            }
        }
/**
 * Check for an admin and 403 if not
 */
        public function mustbeadmin()
        {
            if (!$this->hasadmin())
            {
                $this->web()->noaccess();
            }
        }
/**
 * Check for an developer and 403 if not
 */
        public function mustbedeveloper()
        {
            if (!$this->hasdeveloper())
            {
                $this->web()->noaccess();
            }
        }
/**
  * Do we have a logged in module leader user?
  *
  * @return boolean
  */
        public function ismoduleleader()
        {
            return $this->hasuser() && $this->user()->ismoduleleader();
        }
/**
  * Do we have a logged in project student user?
  *
  * @return boolean
  */
        public function isprojectstudent()
        {
            return $this->hasuser() && $this->user()->isprojectstudent();
        }
/**
  * return project info?
  *
  * @return boolean
  */
        public function returnProjectInfo()
        {
            return $this->user()->getProjectInfo();
        }
/**
  * Do we have a logged in theme leader user?
  *
  * @return boolean
  */
        public function isthemeleader()
        {
            return $this->hasuser() && $this->user()->isthemeleader();
        }
/**
  * Do we have a logged in staff member?
  *
  * @return boolean
  */
        public function isstaff()
        {
            return $this->hasuser() && $this->user()->isstaff();
        }
/**
  * Do we have a logged in project supervisor user?
  *
  * @return boolean
  */
        public function isprojectsupervisor()
        {
            return $this->hasuser() && $this->user()->isprojectsupervisor();
        }
/**
  * Do we have a logged in project supervisor user?
  *
  * @param string   $userid
  *
  * @return boolean
  */
        public function issupervisor($userid)
        {
            return is_object(R::findOne('role', 'user_id = ? AND rolename_id = ?', [$userid, '6']));
        }
/**
  * Do we have a logged in project supervisor user?
  *
  * @param string   $userid
  *
  * @return boolean
  */
        public function isleader($userid)
            {
                return is_object(R::findOne('role', 'user_id = ? AND rolename_id = ?', [$userid, '8']));
            }
/*
 ***************************************
 * Miscellaneous utility functions
 ***************************************
 */
/**
 * Generates a new, unique, sequential id value
 *
 * @param string	$id The prefix for the id
 *
 * @return string
 */
        public function newid($str = 'id')
        {
            $this->idgen += 1;
            return $str.$this->idgen;
        }
/**
 * Check to see if there is a session and return a specific value form it if it exists
 *
 * @param string	$var	The variable name
 * @param boolean	$fail	If TRUE then exit with an error returnif the value  does not exist
 *
 * @return mixed
 */
        public function sessioncheck($var, $fail = TRUE)
        {
            if (isset($_COOKIE[ini_get('session.name')]))
            {
                session_start();
                if (isset($_SESSION[$var]))
                {
                    return $_SESSION[$var];
                }
            }
            if ($fail)
            {
                $this->web()->noaccess();
            }
            return NULL;
        }
/**
 * Generate a Location header for within this site
 *
 * @param string		$where		The page to divert to
 * @param boolean		$temporary	TRUE if this is a temporary redirect
 * @param string		$msg		A message to send
 * @param boolean		$nochange	If TRUE then reply status codes 307 and 308 will be used rather than 301 and 302
 *
 * @return void NEVER returns
 */
        public function divert($where, $temporary = TRUE, $msg = '', $nochange = FALSE)
        {
            $this->web()->relocate($this->local()->base().$where, $temporary, $msg, $nochange);
        }

/**
 * Load a bean or fail with a 400 error
 *
 * @param string	$table	    A bean type name
 * @param integer	$id	    A bean id
 * @param integer       $onerror    A flag indicating what to do on error (see constants above)
 *
 * @return object
 */
        public function load($bean, $id, $onerror = self::R400)
        {
            $foo = R::load($bean, $id);
            if ($foo->getID() == 0)
            { # a bean with that id does not exist
                switch ($onerror)
                {
                case self::R400:
                    $this->web()->bad($bean.' '.$id);

                case self::RNULL:
                    return NULL;

                case self::RTHROW:
                    throw new Exception('No such bean');

                default:
                    throw new Exception('Weird error');
                }
            }
            return $foo;
        }
/**
 * Return the local object
 *
 * @return object
 */
        public function local()
        {
            return Local::getinstance();
        }
/**
 * Return the Formdata object
 *
 * @return object
 */
        public function formdata()
        {
            return Formdata::getinstance();
        }
/**
 * Return the Web object
 *
 * @return object
 */
        public function web()
        {
            return Web::getinstance();
        }
/**
 * Return an iso formatted time for NOW  in UTC
 *
 * @return string
 */
        public function utcnow()
        {
            return R::isodatetime(time() - date('Z'));
        }
/**
 * Return an iso formatted time in UTC
 *
 * @param string       $datetime
 *
 * @return string
 */
        public function utcdate($datetime)
        {
            return R::isodatetime(strtotime($datetime) - date('Z'));
        }
/*
 ***************************************
 * Setup the Context - the constructor is hidden in Singleton
 ***************************************
 */
 /**
 * Initialise the context and return self
 *
 * @param boolean	$local	The singleton local object
 *
 * @return object
 */
        public function setup()
        {
            $this->luser = $this->sessioncheck('user', FALSE); # see if there is a user variable in the session....
            foreach (getallheaders() as $k => $v)
            {
                if (self::TOKEN === strtoupper($k))
                {
                    $tok = JWT::decode($v, self::KEY);
                    $this->luser = $this->load('user', $tok->sub);
                    $this->tokauth = TRUE;
                    break;
                }
            }
            if (isset($_SERVER['REDIRECT_URL']) && !preg_match('/index.php/', $_SERVER['REDIRECT_URL']))
            {
/**
 *  Apache v 2.4.17 changed the the REDIRECT_URL value to be a full URL, so we need to strip this.
 *  Older versions will not have this so the code will do nothing.
 */
                $uri = preg_replace('#^https?://[^/]+#', '', $_SERVER['REDIRECT_URL']);
            }
            else
            {
                $uri = $_SERVER['REQUEST_URI'];
            }
            if ($_SERVER['QUERY_STRING'] != '')
            { # there is a query string so get rid it of it from the URI
                list($uri) = explode('?', $uri);
            }
            $req = array_filter(explode('/', $uri)); # array_filter removes empty elements - trailing / or multiple /
/*
 * If you know that the base directory is empty then you can delete the next test block.
 *
 * You can also optimise out the loop if you know how deep you are nested in sub-directories
 *
 * The code here is to make it easier to move your code around within the hierarchy. If you don't need
 * this then optimise the hell out of it.
 */
            if ($this->local()->base() != '')
            { # we are in at least one sub-directory
                $bsplit = array_filter(explode('/', $this->local()->base()));
                foreach (range(1, count($bsplit)) as $c)
                {
                    array_shift($req); # pop off the directory name...
                }
            }
            if (!empty($req))
            { # there was something after the domain name so split it into action and rest...
                $this->reqaction = strtolower(array_shift($req));
                $this->reqrest = empty($req) ? [''] : array_values($req);
            }

            return $this;
        }
    }
?>
