#!/usr/bin/python
import inspect
import re
import urllib2
import threading
import sys

datei = open("#3.Checken.gestartet", "w")
datei.close()

class read_file_ip():
    """
    Read File line per line
    """
    def __init__(self, file):
         try:
             self.file = open(file, "r+")
         except:
             print "[ERROR] Cant open File"

         self.actual_line = ""


    def next_line(self):
         """
         Moves the pointer to the next line and returns this
         """
         try:
             line = self.file.next().rstrip()
         except StopIteration:
             line = False

         except AttributeError:
             line = False

         self.actual_line = line
         return line

    def actual_line(self):
         """
         Returns actual line, doesnt moves the pointer
         """
         return self.actual_line



class tools():
    """
    Here you can store all functions which you want to use a few times
    """
    @staticmethod
    def logging(file, value):
        """
        Log something to a file
        """
        log_file = open(file, "a")
        log_file.write(value+"\r\n")
        log_file.close()

    @staticmethod
    def create_http_url(host, port, file = "/", prot = "http"):
        """
        Create Url for Urllib2
        """
        return "%s://%s:%s%s" %(prot, host, port, file)

    @staticmethod
    def http_get(ip, port, file = "",  timeout = None, url = None, ssl = False):
        """
        GET HTTP Status Code, html and url from url or ip + file
        """
        if url == None:
            if ssl == False:
                prot = "http"
            else:
                prot = "https"
            if port == None:
                port = 80

            if file == None:
                raise("ERR: func_http_get: no url or file specified")
            url = "%s://%s:%s%s" %(prot, ip, port, file)

        if timeout == None:
             timeout = scan.conf_timeout

        try:
            conn = urllib2.urlopen(url, timeout = timeout)
        except urllib2.HTTPError as e:        
            return [True, e.code, url, e.read()]

        except urllib2.URLError as e:
            return [False, None, None, None]

        except urllib2.socket.timeout as e:
            return [False, None, None, None]

        except ssl.SSLError as e:
	    return [False, None, None, None]

        return [True, conn.code, url, conn.read()]

    @staticmethod
    def get_string_between(string, start, end):
        try:
            end_of_string = string.index(start) + len(start)
            start_of_string = string.index(end, end_of_string)
            return string[end_of_string:start_of_string]
        except:
            return False  

    @staticmethod
    def get_http_headers(url, timeout = None):
        """
        Get Http Headers and compare them to dictionary which will be returned
        """
        main_url = url

        target_headers_dict = {}
        if timeout == None:
            timeout = scan.conf_timeout
        try:
            target_urllib = urllib2.urlopen(main_url, timeout = timeout)
        except urllib2.HTTPError as e:        
            return {"Error" : e}

        except urllib2.URLError as e:
            return {"Error" : e}

        except urllib2.socket.timeout as e:
            return {"Error" : e}
        
        except:
            return {"Error" : "Unknown"}


        target_headers = target_urllib.info().headers
        for i in target_headers:
            i = i.strip()
            items = i.split(": ")
            try:
                target_headers_dict[items[0]] = items[1]
            except IndexError:
                print items   #Heres some bug but to lazy to fix it ^^ Fixxed with try but no nice code...

        return target_headers_dict

    def check_if_any_from_arr_in_string(string, whitelist = None, blacklist = None):
        """
        Check if any item from array is in string.
        Allows black and whitelist.
        """
        if whitelist == None and blacklist == None:
            return False
        elif whitelist == None:
            whitelist == []
        elif blacklist == None:
            blacklist == []
        for i in blacklist:
            print i
        if any(k in string for k in whitelist) and any(k not in string for k in blacklist):
            return True
        else:
            return False

    def regex_not_match(string, regex):
        """
        Returns True if regex does NOT match, false if it matches. Needed for check_if_any_reg_from_arr_in_string()
        """
        if re.match(regex, string) == None:
            return True
        else:
            return False


    def check_if_any_reg_from_arr_in_string(string, whitelist = None, blacklist = None):
        """
        Checks if any regex from array is in string.
        Allows black and whitelist.
        """
        if whitelist == None and blacklist == None:
            return False
        elif whitelist == None:
            whitelist == []
        elif blacklist == None:
            blacklist == []
        if any(re.match(k, string) for k in whitelist) and any(regex_not_match(string, k) for k in blacklist):
            return True
        else:
            return False

    @staticmethod
    def http_basic_auth(theurl, username, password):
        passman = urllib2.HTTPPasswordMgrWithDefaultRealm()
        # this creates a password manager
        passman.add_password(None, theurl, username, password)
        # because we have put None at the start it will always
        # use this username/password combination for  urls 
        # for which `theurl` is a super-url

        authhandler = urllib2.HTTPBasicAuthHandler(passman)
        # create the AuthHandler

        opener = urllib2.build_opener(authhandler)

        urllib2.install_opener(opener)
         # All calls to urllib2.urlopen will now use our handler
        # Make sure not to include the protocol in with the URL, or
        # HTTPPasswordMgrWithDefaultRealm will be very confused.
        # You must (of course) use it when fetching the page though.
        try:
            pagehandle = urllib2.urlopen(theurl)
        except urllib2.HTTPError as e:
            return [False, e]
    
        # authentication is now handled automatically for us
        return [True, pagehandle.read()]
    
class scan():
    """
    Class which does the Scanning Part.
    Here you can also add new scan modules.
    """
    def __init__(self, timeout):
        self.mod_scan_list = []
        self.func_scan_modules()
        self.conf_timeout = int(timeout) 


    def check(self, ip, port):
        print "Scanning ",ip, port
        for mod in self.mod_scan_list:
            #print mod
            eval("self.module_scan_%s(\"%s\", %s)" %(mod, ip, port))

        print "Finished ",ip, port

    def func_scan_modules(self):


        all_funcs = inspect.getmembers(self, inspect.ismethod)
        for func in all_funcs:
            func_name = eval("self.%s" %(func[0]))
            func_args = inspect.getargspec(func_name)
            func_real_name_split = func[0].split("_")
            #print func_args
            if func_real_name_split[0] == "module":
                if func_real_name_split[1] == "scan":
                    self.mod_scan_list.append(func_real_name_split[2])
                    print "[Module] Scan: %s" %(func_real_name_split[2])
     
    #def module_scan_webdav(self, ip, port):
    #    print "im runnin"

    def module_scan_drupal(self, ip, port):
        #
        # Scan for Drupal (all versions)
        #
        __info__ = {"name" : "drupal",
                    "log_result_file" : "log_drupal.txt",
                    "paths" : ["/", "/drupal/", "/cms/"],}
        
        main_url = tools().create_http_url(ip, port, file = "", prot = "http")
        
        for path in __info__['paths']:
           main_server_info = tools().get_http_headers(main_url+path)
           if main_server_info.get("Expires") == "Sun, 19 Nov 1978 05:00:00 GMT":
              tools().logging(__info__['log_result_file'], main_url+path+" Server:"+path+ "  "+main_server_info.get("X-Generator"))

    def module_scan_mysqldumper(self, ip, port):
        #
        # Scan Hosts for installed mysqldumper and log them
        #
        __info__ = {"name" : "mysqldumper",
                    "log_usec_result_file" : "usec_result_msd.txt",
                    "log_sec_result_file" : "sec_results_msd.txt",
                    "log_unknw_result_file" : "uknwn_results_msd.txt",
                    "paths" : ["/msd", "/mySqlDumper", "/msd1.24stable", "/msd1.24.4", "/mysqldumper", "/MySQLDumper", "/mysql", "/sql"]}

        main_url = tools().create_http_url(ip, port, file = "", prot = "http")
        #print main_url
        for path in __info__['paths']:
             target_url = main_url+path
             target_return = tools().http_get(None, None, url = target_url)
             if target_return[0] == False:
                 print "Host down"
                 break

             if target_return[1] == 200:
                 #Might be unsecured
                 if target_return[3].find("<title>MySQLDumper</title>") != -1:
                     print "[*] MSD (UNSEC):",target_url
                     tools().logging(__info__['log_usec_result_file'], target_url)

                 else:
                     tools().logging(__info__['log_unknw_result_file'], target_url)
                 
             elif target_return[1] == 203:
                 #Might be protected with htaccess
                 print "[*] MSD (SEC):",target_url
                 tools().logging(__info__['log_sec_result_file'], target_url)

    def module_scan_phpcgi(self, ip, port):
        #
        # Scan Hosts for installed mysqldumper and log them
        #
        __info__ = {"name" : "phpcgi",
                    "log_usec_result_file" : "php_cgi.txt",
                    "paths" : ["/cgi-bin/php", "/cgi-bin/php5"]}

        main_url = tools().create_http_url(ip, port, file = "", prot = "http")
        #print main_url
        for path in __info__['paths']:
             target_url = main_url+path
             target_return = tools().http_get(None, None, url = target_url)
             if target_return[0] == False:
                 print "Host down"
                 break

             if target_return[1] == 200:
                 tools().logging(__info__['log_usec_result_file'], target_url)

    def module_scan_ejbinvoker(self, ip, port):
        __info__ = {"name" : "EJBInvokerServlet",
                    "log_usec_result_file" : "usec_result_ejb.txt",
                    "log_sec_result_file" : "sec_results_ejb.txt",
                    "log_unknw_result_file" : "uknwn_results_ejb.txt",
                   "paths" : ["/status?full=true"],
                   "marks" : ["EJBInvokerServlet"]}
       
        main_url = tools().create_http_url(ip, port, file = "", prot = "http")
        #print main_url
        for path in __info__['paths']:
             target_url = main_url+path
             target_return = tools().http_get(None, None, url = target_url)
             if target_return[0] == False:
                 print "Host down"
                 break

             if target_return[1] == 200:
                 #Might be unsecured
                 # Check with k not in ... mark_fuzzed that the pma is not fucked up ;)
                 if any(k in target_return[3] for k in __info__['marks']):
                     print "[*] EJB (USEC):",target_url
                     tools().logging(__info__['log_usec_result_file'], target_url)
                 else:
                     print "[*] EJB (UKNWN):",target_url
                     tools().logging(__info__['log_unknw_result_file'], target_url)
                 
             elif target_return[1] == 203:
                 #Might be protected with htaccess
                 print "[*] EJB (SEC):",target_url
                 tools().logging(__info__['log_sec_result_file'], target_url)

    def module_scan_jenkins(self, ip, port):
        #
        # Scan Hosts for installed Jenkins Server and log them
        #
        __info__ = {"name" : "jenkins",
                    "log_usec_result_file" : "usec_result_jenkins.txt",
                    "log_create_result_file" : "create_results_jenkins.txt",
                    "log_sec_result_file" : "sec_results_jenkins.txt",
                    "log_unknw_result_file" : "uknwn_results_jenkins.txt",
                    "paths" : ["/script", "/jenkins/script", "/login"]}

        main_url = tools().create_http_url(ip, port, file = "", prot = "http")
        #print main_url
        for path in __info__['paths']:
             target_url = main_url+path
             target_return = tools().http_get(None, None, url = target_url)
             print target_return[1]
             if target_return[0] == False:
                 print "Host down"
                 break

             if target_return[1] == 200:
                 #Might be unsecured
                 if target_return[3].find("println(Jenkins.instance.pluginManager.plugins)") != -1:
                     print "[*] Jenkins (UNSEC):",target_url
                     tools().logging(__info__['log_usec_result_file'], target_url)
                     print target_return[3]

		 elif target_return[3].find("\">Create an account</a> if you are not a member yet.</div></div></td></tr>") != -1:
                     #might create account
                     print "[*] Jenkins (CREATE):",target_url
                     tools().logging(__info__['log_create_result_file'], target_url)
                     print target_return[3]

		 elif target_return[3].find("<title>Jenkins</title>") != -1:
                     print "[*] Jenkins (SEC):",target_url
                     tools().logging(__info__['log_sec_result_file'], target_url)
                     print target_return[3]

                 else:
                     tools().logging(__info__['log_unknw_result_file'], target_url)

             elif target_return[1] == 203:
                 #Might be protected with htaccess
                 print "[*] Jenkins (SEC):",target_url
                 tools().logging(__info__['log_sec_result_file'], target_url)

    def module_scan_phpmyadmin(self, ip, port):
        #
        # Scan Hosts for installed mysqldumper and log them
        #
        __info__ = {"name" : "phpmyadmin",
                    "log_usec_result_file" : "usec_result_pma.txt",
                    "log_sec_result_file" : "sec_results_pma.txt",
                    "log_unknw_result_file" : "uknwn_results_pma.txt",
                    "paths" : ["/phpmyadmin", "/phpMyAdmin", "/mysql", "/sql", "/myadmin", "/phpMyAdmin-4.2.1-all-languages", "/phpMyAdmin-4.2.1-english", "/xampp/phpmyadmin", "/typo3/phpmyadmin", "/webadmin"],
                    "mark_usec" : ["<li id=\"li_server_info\">Server: ", "src=\"navigation.php", "src=\"main.php"],
                    "mark_sec" : ["www.phpmyadmin.net", "input_username", "pma_username", "pma_password", "src=\"main.php?token="],
                    "mark_blacklist" : ["<?php", "<?"]}

        main_url = tools().create_http_url(ip, port, file = "", prot = "http")
        #print main_url
        for path in __info__['paths']:
             target_url = main_url+path
             target_return = tools().http_get(None, None, url = target_url)
             if target_return[0] == False:
                 print "Host down"
                 break

             if target_return[1] == 200:
                 #Might be unsecured
		 #print target_return[3]
                 # Check with k not in ... mark_fuzzed that the pma is not fucked up ;)
                 if any(k in target_return[3] for k in __info__['mark_usec']) and any(k not in target_return[3] for k in __info__['mark_blacklist']):
                     print "[*] PMA (USEC):",target_url
                     tools().logging(__info__['log_usec_result_file'], target_url+"/index.php")
                 elif any(k in target_return[3] for k in __info__['mark_sec']) and any(k not in target_return[3] for k in __info__['mark_blacklist']): 
                     print "[*] PMA (SEC):",target_url
                     tools().logging(__info__['log_sec_result_file'], target_url+"/index.php")
                 else:
                     print "[*] PMA (UKNWN):",target_url
                     tools().logging(__info__['log_unknw_result_file'], target_url+"/index.php")
                 
             elif target_return[1] == 203:
                 #Might be protected with htaccess
                 print "[*] PMA (SEC):",target_url
                 tools().logging(__info__['log_sec_result_file'], target_url)

    def module_scan_httpserver(self, ip, port):
        #
        # Log HTTPServer Information such as used Serversoftware and Version if possible
        #
        __info__ = {"name" : "httpserverinfo",
                    "log_result_file" : "log_httpserver.txt"}

        target_url = tools().create_http_url(ip, port, file = "", prot = "http")
        headers = tools().get_http_headers(target_url)
        try:
            headers_server = headers['Server']
        except KeyError, TypeError:
            headers_server = "Unknown"


        #print headers_server
        tools().logging(__info__['log_result_file'], target_url+" Server:"+headers_server)

    def module_scan_jmx(self, ip, port):
        #
        # Scan for Jboss or Tomcat servers having a admin panel
        #
        __info__ = {"name" : "jmx",
                    "log_sec_result_file" : "log_jmx_secured.txt",
                    "log_usec_result_file" : "log_jmx_unsecured.txt",
                    "log_unknw_result_file" : "log_jmx_unknwn.txt",
                    "log_bruted_result_file" : "log_jmx_bruted.txt",
                    "paths" : ["/jmx-console", "/manager/html"],
                    "mark_sec" : ["main Manager page", "&lt;role rolename=\"manager-gui\"/&gt;", "Manager App HOW-TO"],
                    "mark_usec" : ["JBoss JMX Management Console"]}

        main_url = tools().create_http_url(ip, port, file = "", prot = "http")

        for path in __info__['paths']:
             target_url = main_url+path

             target_return = tools().http_get(None, None, url = target_url)

             #print target_return[1]
             if target_return[0] == False:
                 print "Host down"
                 break

             if target_return[1] == 404:
                 continue #Skip 404 Things


             target_server_info = tools().get_http_headers(main_url)
             headers = tools().get_http_headers(main_url)
             try:
                 headers_server = headers['Server']
             except KeyError:
                 headers_server = "Unknown" 
             result_line = "%s Server: %s" %(target_url, headers_server)

             if target_return[1] == 200 or target_return[1] == 401:
                 #Might be unsecured
                 if any(k in target_return[3] for k in __info__['mark_sec']):
                     if tools().http_basic_auth(target_url, "tomcat", "tomcat")[0] == True:
                         result_line += " Account: tomcat / tomcat"
                         print "[*] JMX (BRUTED):",target_url, "Login: tomcat:tomcat"
                         tools().logging(__info__['log_bruted_result_file'], result_line)
                     elif tools().http_basic_auth(target_url, "admin", "tomcat")[0] == True:
                         result_line += " Account: admin / tomcat"
                         print "[*] JMX (BRUTED):",target_url, "Login: admin:tomcat"
                         tools().logging(__info__['log_bruted_result_file'], result_line)
                     elif tools().http_basic_auth(target_url, "tomcat", "t0mcat")[0] == True:
                         result_line += " Account: tomcat / t0mcat"
                         print "[*] JMX (BRUTED):",target_url, "Login: tomcat:t0mcat"
                         tools().logging(__info__['log_bruted_result_file'], result_line)

                     else:
                         print "[*] JMX (SEC):",target_url
                         tools().logging(__info__['log_sec_result_file'], result_line)
                 elif any(k in target_return[3] for k in __info__['mark_usec']):
                     print "[*] JMX (USEC):",target_url
                     tools().logging(__info__['log_usec_result_file'], result_line)
                 else:
                     print "[*] JMX (UKNWN):",target_url
                     tools().logging(__info__['log_unknw_result_file'], result_line)

             elif target_return[1] != 404:
                 #Needs Login but can be bruted
                 print "[*] JMX (UNKNWN):",target_url
                 tools().logging(__info__['log_unknw_result_file'], result_line)

    def module_scan_webdav(self, ip, port):
        #
        # Scan for Jboss or Tomcat servers having a admin panel
        #
        __info__ = {"name" : "webdav",
                    "log_result_file" : "log_webdav.txt",
                    "log_unknw_result_file" : "log_webdav_unknwn.txt",
                    "paths" : ["/webdav"],
                    "mark_xampp" : ["<b>WebDAV testpage</b>"]}

        main_url = tools().create_http_url(ip, port, file = "", prot = "http")

        for path in __info__['paths']:
             target_url = main_url+path

             target_return = tools().http_get(None, None, url = target_url)

             #print target_return[1]
             if target_return[0] == False:
                 print "Host down"
                 break

             if target_return[1] == 404:
                 continue #Skip 404 Things


             target_server_info = tools().get_http_headers(main_url)
             headers = tools().get_http_headers(main_url)
             try:
                 headers_server = headers['Server']
             except KeyError:
                 headers_server = "Unknown" 
             result_line = "%s Server: %s" %(target_url, headers_server)

             if target_return[1] == 200 or target_return[1] == 401:
                 if any(k in target_return[3] for k in __info__['mark_xampp']):
                     print "[*] WebDAV (TRUE):", target_url
                     tools().logging(__info__['log_result_file'], result_line)
                 else:
                     tools().logging(__info__['log_unknw_result_file'], result_line)
                 

             elif target_return[1] != 404:
                 #Needs Login but can be bruted
                 print "[*] WebDAV (UNKNWN):",target_url
                 tools().logging(__info__['log_unknw_result_file'], result_line)

    def module_scan_sqlitemanager(self, ip, port):
        __info__ = {"name" : "sqlitemanager",
                    "log_result_file" : "log_sqlitemanager.txt",
                    "log_unknwn_result_file" : "log_unknwn_sqlitemanager.txt",
                    "paths" : ["/sqlite", "/SQLite/SQLiteManager-1.2.4", "/SQLiteManager-1.2.4", "/sqlitemanager", "/SQlite", "/SQLiteManager"],
                    "marks" : ["Create or add new database", "<h2 class=\"sqlmVersion\">Welcome to", "http://www.sqlitemanager.org"],}
        main_url = tools().create_http_url(ip, port, file = "", prot = "http")
        main_server_info = tools().get_http_headers(main_url)
        for path in __info__['paths']:
            target_url = main_url+path+"/main.php"
            
            target_return = tools().http_get(None, None, url = target_url)
            if target_return[1] == 200:
                result_line = "%s Server: %s" %(target_url, main_server_info['Server'])
                if any(k in target_return[3] for k in __info__['marks']):
                    
                    sys.stdout.write("[*] Sqlitemanager: %s\n" %target_url)
                    tools().logging(__info__['log_result_file'], result_line)
                else:
                    tools().logging(__info__['log_unknwn_result_file'], result_line)
                

    def module_scan_joomla(self, ip, port):
        #
        # Scan for Jboss or Tomcat servers having a admin panel
        #
        __info__ = {"name" : "joomla",
                    "log_result_file" : "log_joomla.txt",
                    "log_unknw_result_file" : "log_joomla_unknwn.txt",
                    "paths" : ["/", "/joomla", "/cms", "/Joomla"],
                    "marks" : ["Joomla!", "http://www.joomla.org", "for=\"modlgn_username\">"],
                    "marks_1.5x" : ["<meta name=\"generator\" content=\"Joomla! 1.5 - Open Source Content Management\" />"]}
        
        main_url = tools().create_http_url(ip, port, file = "", prot = "http")
        main_server_info = tools().get_http_headers(main_url)
        for path in __info__['paths']:
            
            target_url = main_url+path+"/administrator"
            target_return = tools().http_get(None, None, url = target_url)
            #print target_return[3]
            if target_return[1] == 200:
                if any(k in target_return[3] for k in __info__['marks']):
                    joomla_version = "UNKNOWN"
                    if any(k in target_return[3] for k in __info__['marks_1.5x']):
                        joomla_version = "1.5.x" 

                    result_line = "%s Version: %s Server: %s" %(main_url+path ,joomla_version, main_server_info['Server'])
                    print "[*] JOOMLA:", target_url, "Version:", joomla_version
                    tools().logging(__info__['log_result_file'], result_line)

    def module_scan_wordpress(self, ip, port):
        #
        # Scan for Jboss or Tomcat servers having a admin panel
        #
        __info__ = {"name" : "wordpress",
                    "log_result_file" : "log_wordpress.txt",
                    "log_unknw_result_file" : "log_wordpress_unknwn.txt",
                    "paths" : ["/", "/wordpress", "/wp", "/blog", "/Wordpress", "/Blog"],
                    "marks" : ["wp-submit", "wp_attempt_focus()", "Powered by WordPress", "?action=lostpassword"],}
        
        main_url = tools().create_http_url(ip, port, file = "", prot = "http")
        main_server_info = tools().get_http_headers(main_url)
        for path in __info__['paths']:
            
            target_url = main_url+path+"/wp-login.php"
            target_return = tools().http_get(None, None, url = target_url)
            #print target_return[3]
            if target_return[1] == 200:
                if any(k in target_return[3] for k in __info__['marks']):

                    result_line = "%s Server: %s" %(main_url+path, main_server_info['Server'])
                    print "[*] WordPress:", target_url
                    tools().logging(__info__['log_result_file'], result_line)


class main():
    """
    Main part which controls the complete program
    """
    def __init__(self, file, timeout = 10):
        self.file = read_file_ip(file)
        global scan
        scan = scan(timeout)

    def run(self, threads):
        threads = int(threads)
        print "[INFO] Scanning with %s Thread(s)" %threads

        while True:
            line = self.file.next_line()
            if line == False:
                 break
            while True:
                if threading.active_count() <= threads:
                   ip_port = line.split(":")
                   if(re.match("((25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)\.){3}(25[0-5]|2[0-4]\d|1\d\d|[1-9]\d|\d)",ip_port[0])   != None):
                       ip = ip_port[0]
                       port = ip_port[1].split(" ")[0]
                       t = threading.Thread(target=scan.check, args=(ip, port))
                       t.deamon = False
                       t.start()
                       break
                   else:
                       break
        return True


if __name__ == "__main__":
    __version__ = "0.1"
    def help():
        print "-------------------------------------------"
        print "-          pyVulnscanner %s              -" %__version__
        print "- .py <file> <threads> [timeout]          -"
        print "--              ***                      --"
        print "- Greetz fly out to:                      -"
        print "-  ddr, b2r, bwc, il, maro, burnz, chucky,-"
        print "-  gil, bebop, Gnu, airy, fake,           -"
        print "-  dodo, mani, Buster and all i foget :D  -"
        print "-------------------------------------------"

    if len(sys.argv) == 3:
        main = main(sys.argv[1])
        main.run(sys.argv[2])
    elif len(sys.argv) == 4:
        main = main(sys.argv[1], timeout = sys.argv[3])
        main.run(sys.argv[2])
        
    else:
        help()
		
datei = open("#3.Checken.fertig", "w")
datei.close()