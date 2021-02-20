#
# g++ Makefile on Linux 
#
# -o output file 
# -c compile but not link 
# -L, -I include and lib paths 
# -l link a lib 
# -g insert debuggin info in your executable
# -Wall turn on warnings 
# -fPIC position independent code (does't work sometimes)
# -O optimize
#
#
#
# /usr/lib/x86_64-linux-gnu 
# /usr/include/mysql-cppconn-8
# /usr/include/boost
#
#
#

CC = g++
CFLAGS= -Wall -I/usr/include/mysql-cppconn-8/jdbc
LDFLAGS =  -L/usr/lib/x86_64-linux-gnu  -lmysqlcppconn

all: provisioning

provisioning: provisioning.cpp
	$(CC) $(CFLAGS) -o provisioning provisioning.cpp $(LDFLAGS)

clean:
	rm -f *.o provisioning
