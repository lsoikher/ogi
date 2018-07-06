#!/usr/bin/python

import time, socket, struct, threading, sys

datei = open("#1.scan.gestartet", "w")
datei.close()

class file():
    def __init__(self, file):
        self.file = open(file, "r")

    def next_line(self):
        try:
            next_line = self.file.next().rstrip()
        except StopIteration:
            next_line = False
        self.act_line = next_line
        return self.act_line
        
    def actual_line(self):
        return self.act_line
        

class class_tools():

    def __init__(self):
        pass
        
    def init_iprange(self, start, end):
        """
        Init iprange, must be called before iprange_nextip!
        """
        self.var_iprange_start = start
        self.var_iprange_end = end
        self.var_iprange_now = None

    def iprange_nextip(self):
        """
        Calc next ip for range defined wiht init_iprange.
        If Range is finished it returns False.
        """
        if self.var_iprange_now == None:
            self.var_iprange_now = self.var_iprange_start
        elif self.var_iprange_now != self.var_iprange_end:
            self.var_iprange_now = self.ip2nextip(self.var_iprange_now)
        else:
            return False
        return self.var_iprange_now
            
            
        
    def ip2nextip(self, ip):
        """
        Calc next the next ip
        """
        long_ip = self.ip2long(ip)
        long_ip += 1
        next_ip = self.long2ip(long_ip)
        return next_ip

    def ip2long(self, ip):
        """
        Convert ip to a long
        """
        packed = socket.inet_aton(ip)
        return struct.unpack("!L", packed)[0]

    def long2ip(self, n):
        """
        Convert a long to ip
        """
        unpacked = struct.pack('!L', n)
        return socket.inet_ntoa(unpacked)
        
    def ipportopen(self, host, port, timeout = 10):
        """
        Check if a port is open and return true or false
        """
        s = socket.socket()
        s.settimeout(timeout)
        try:
            s.connect((host, port))
        except socket.error as e:
            e = str(e)
            return [False, e]
        
        return [True]

    def logging(self, file, value):
        """
        Append value to file
        """
        log_file = open(file, "a")
        log_file.write(value+"\r\n")
        log_file.close()
        
    def range_line_struct(self, line):
        """
        structure/parse a range line
        """
        tmp_line = line.split(" ")
        # To do:
        #  - add regex to match if valid ip
        if len(tmp_line) != 2:
            return False
        else:
            return [tmp_line[0], tmp_line[1]]

    def createDaemon():

        try:
            # Fork a child process so the parent can exit.  This returns control to
            # the command-line or shell.  It also guarantees that the child will not
            # be a process group leader, since the child receives a new process ID
            # and inherits the parent's process group ID.  This step is required
            # to insure that the next call to os.setsid is successful.
            pid = os.fork()
        except OSError, e:
            raise Exception, "%s [%d]" % (e.strerror, e.errno)

        if (pid == 0):   # The first child.
            # To become the session leader of this new session and the process group
            # leader of the new process group, we call os.setsid().  The process is
            # also guaranteed not to have a controlling terminal.
            os.setsid()

            # Is ignoring SIGHUP necessary?
            #
            # It's often suggested that the SIGHUP signal should be ignored before
            # the second fork to avoid premature termination of the process.  The
            # reason is that when the first child terminates, all processes, e.g.
            # the second child, in the orphaned group will be sent a SIGHUP.
            #
            # "However, as part of the session management system, there are exactly
            # two cases where SIGHUP is sent on the death of a process:    
            #
            #   1) When the process that dies is the session leader of a session that
            #      is attached to a terminal device, SIGHUP is sent to all processes
            #      in the foreground process group of that terminal device.
            #   2) When the death of a process causes a process group to become
            #      orphaned, and one or more processes in the orphaned group are
            #      stopped, then SIGHUP and SIGCONT are sent to all members of the
            #      orphaned group." [2]
            #
            # The first case can be ignored since the child is guaranteed not to have
            # a controlling terminal.  The second case isn't so easy to dismiss.
            # The process group is orphaned when the first child terminates and
            # POSIX.1 requires that every STOPPED process in an orphaned process
            # group be sent a SIGHUP signal followed by a SIGCONT signal.  Since the
            # second child is not STOPPED though, we can safely forego ignoring the
            # SIGHUP signal.  In any case, there are no ill-effects if it is ignored.
            #
            # import signal           # Set handlers for asynchronous events.
            # signal.signal(signal.SIGHUP, signal.SIG_IGN)
            try:
                # Fork a second child and exit immediately to prevent zombies.  This
                # causes the second child process to be orphaned, making the init
                # process responsible for its cleanup.  And, since the first child is
                # a session leader without a controlling terminal, it's possible for
                # it to acquire one by opening a terminal in the future (System V-
                # based systems).  This second fork guarantees that the child is no
                # longer a session leader, preventing the daemon from ever acquiring
                # a controlling terminal.
                pid = os.fork()    # Fork a second child.
            except OSError, e:
                raise Exception, "%s [%d]" % (e.strerror, e.errno)
            if (pid == 0):    # The second child.
                # Since the current working directory may be a mounted filesystem, we
                # avoid the issue of not being able to unmount the filesystem at
                # shutdown time by changing it to the root directory.
                # os.chdir(WORKDIR) EDIT by Whyned: Not important for me ;)
                # We probably don't want the file mode creation mask inherited from
                # the parent, so we give the child complete control over permissions.
                #os.umask(UMASK) EDIT by Whyned: Not important for me ;)
                pass
            else:
                # exit() or _exit()?  See below.
                os._exit(0)    # Exit parent (the first child) of the second child.
        else:
            # exit() or _exit()?
            # _exit is like exit(), but it doesn't call any functions registered
            # with atexit (and on_exit) or any registered signal handlers.  It also
            # closes any open file descriptors.  Using exit() may cause all stdio
            # streams to be flushed twice and any temporary files may be unexpectedly
            # removed.  It's therefore recommended that child branches of a fork()
            # and the parent branch(es) of a daemon use _exit().
            os._exit(0)   # Exit parent of the first child.
    
        
def func_help():
    print """
:: pyRangeScanner v%s ::
    
With this Tool you can scan a range for (multiple) open port(s)
It can handle a single range or a file with multiple ranges and
it supports threads.
  
:: HELP ::

.py -r range_start range_end ports threads [timeout]
.py -rf range_file ports threads [timeout]

ports = 80 or for multiple ports 80,8080,81...

Default Timeout = %s

:: EXAMPLE ::

.py -r 127.0.0.0 127.0.1.0 80,8080,22 20 10 
.py -rf xyz.txt 80,8080,22 20 10

:: EXAMPLE RANGE FILE ::

127.0.0.0 127.0.1.0
125.1.1.0 125.2.0.0
...

:: GREETS ::

Greets fly out to:
Team DDR, Team WTC, BWC, Inferno-Load, B2R, Datenreiter,
Burnz, Gil, LeChuck, Bebop, Fr0sty, Gnu, Airy, FaKe,
Generation, Shizuko, leety and all i forget!

        """ %(__info__['version'], __info__['def_timeout'])
def func_portcheck(ip, port, timeout):
    """
    Handle return from tools.ipportopen and log to file
    """
    log_result = "result.txt"
    log_failure = "log.txt"
    tmp_ip = tools.ipportopen(ip, port, timeout)
    # sys.stdout.write("[*] Checking: %s %s\n" %(ip, port))
    if tmp_ip[0] != False:
        # sys.stdout.write("[+] %s Port %s Open!\n" %(ip, port))
        tools.logging(log_result, "%s:%s" %(ip, port))
    # elif tmp_ip[0] == False:
        # sys.stdout.write("[-] %s Port %s %s\n" %(ip,port, tmp_ip[1]))
        # tools.logging(log_failure, "%s:%s %s" %(ip, port, tmp_ip[1]))

def func_portcheckv1(ip, port, timeout):
    """
    Handle return from tools.ipportopen and log to file
    port must be array!
    """
    log_result = "result.txt"
    log_failure = "log.txt"
    timeout = int(timeout)
    for tmp_port in port:
        tmp_port = int(tmp_port)
        tmp_ip = tools.ipportopen(ip, tmp_port, timeout)
        # sys.stdout.write("[*] Checking: %s %s\n" %(ip, tmp_port))
        if tmp_ip[0] != False:
            # sys.stdout.write("[+] %s Port %s Open!\n" %(ip, tmp_port))
            tools.logging(log_result, "%s:%s" %(ip, tmp_port))
        
        # elif tmp_ip[0] == False:
        #     sys.stdout.write("[-] %s Port %s %s\n" %(ip, tmp_port, tmp_ip[1]))
        #     # tools.logging(log_failure, "%s:%s %s" %(ip, tmp_port, tmp_ip[1]))

        if tmp_ip[0] == False and tmp_ip[1] == "timed out" or tmp_ip[0] == False and tmp_ip[1] == "[Errno 101] Network is unreachable":  # Delete this if you want to check all ports
            # sys.stdout.write("[-] Skipping other Ports from %s" %(ip))
            break
        
def main1(range_start, range_end, port, timeout):
    """
    Check a Range for open port (single threaded)
    """
    tools.init_iprange(range_start, range_end)
    while True:
        next_ip = tools.iprange_nextip()
        # if next_ip != False:
        #     print next_ip
        #     print tools.ipportopen(next_ip, port, timeout = timeout)
        # else:
        #     break
        if next_ip == False:
            break

def main2(range_start, range_end, port, timeout, threads):
    """
    Check a Range for open port (multi threaded)
    """
    tools.init_iprange(range_start, range_end)
    while True:
        if threading.active_count() < threads:
            next_ip = tools.iprange_nextip()
            if next_ip != False:
                thread = threading.Thread(target=func_portcheck, args=(next_ip, port, timeout,))
                thread.start()

            else:
                break

    while threading.active_count() != 1: #Wait until all threads are finished.
        time.sleep(0.1)
        
def main2v1(range_start, range_end, port, timeout, threads):
    """
    Check a Range for open port(s) (multi threaded)
    port argument must be array!
    """
    threads = int(threads)
    tools.init_iprange(range_start, range_end)
    while True:
        if threading.active_count() <= threads:
            # print threading.active_count(), threads
            next_ip = tools.iprange_nextip()
            if next_ip != False:
                thread = threading.Thread(target=func_portcheckv1, args=(next_ip, port, timeout,))
                thread.start()

            else:
                break

    while threading.active_count() > 2: #Wait until all threads are finished.
        time.sleep(0.1)

def main3(range_file, port, timeout, threads):
    """
    Check Ranges from Range file for open port
    """
    range_file = file(range_file)
    while True: #Read range_file line per line
        line = range_file.next_line()
        if line == False:
            break
        line_split = tools.range_line_struct(line)
        main2(line_split[0], line_split[1], port, timeout, threads)

def main3v1(range_file, port, timeout, threads):
    """
    Check Ranges from Range file for multiple open ports
    port must be array!
    """
    range_file = file(range_file)
    while True: #Read range_file line per line
        line = range_file.next_line()
        if line == False:
            break
        line_split = tools.range_line_struct(line)
        main2v1(line_split[0], line_split[1], port, timeout, threads)
    
    
if __name__ == "__main__":
    global tools, __info__
    __info__ = {}
    __info__['version'] = "0.1"
    __info__['def_timeout'] = 10
    tools = class_tools()
    #main1("173.194.35.151", "173.194.35.160", 80, 2)
    #main2("173.194.35.151", "173.194.35.160", 81, 2, 10)
    #main3("/tmp/test.txt", 80, 2, 10)
    #main2v1("192.168.178.0", "192.168.179.0", [81, 80], 2, 10)
    #main3v1("/tmp/test.txt", [80, 8080, 21], 2, 20)
    # print len(sys.argv),sys.argv
    if len(sys.argv) >= 5:
        if sys.argv[1] == "-rf":
            if len(sys.argv) == 6:
                # Use range_file and timeout
                # .py -rf rangefile port,port,port threads timeout
                range_file = sys.argv[2]
                port = sys.argv[3].split(",")
                threads = int(sys.argv[4])+1
                timeout = sys.argv[5]
                main3v1(range_file, port, timeout, threads)

        
            elif len(sys.argv) == 5:
                # Use range_file and no timeout
                # .py -rf rangefile port,port,port threads (timeout = default = 10)
                range_file = sys.argv[2]
                port = sys.argv[3].split(",")
                threads = int(sys.argv[4])+1
                timeout = __info__['def_timeout']
                main3v1(range_file, port, timeout, threads)
        
            else:
                func_help()
            
        elif sys.argv[1] == "-r":
            if len(sys.argv) == 7:
                # Use a single range and timeout
                # .py -r range_start range_end port,port,port threads timeout
                range_start = sys.argv[2]
                range_end = sys.argv[3]
                port = sys.argv[4].split(",")
                threads = int(sys.argv[5])+1
                timeout = sys.argv[6]
                main2v1(range_start, range_end, port, timeout, threads)

        
            elif len(sys.argv) == 6:
                # Use a single range and no timeout
                # .py -r range_start range_end port,port,port threads
                range_start = sys.argv[2]
                range_end = sys.argv[3]
                port = sys.argv[4].split(",")
                threads = int(sys.argv[5])+1
                timeout = __info__['def_timeout']
                main2v1(range_start, range_end, port, timeout, threads)
            
            else:
                func_help()
            
        else:
            func_help()
    
    else:
        func_help()

datei = open("#2.scan.fertig", "w")
datei.close()