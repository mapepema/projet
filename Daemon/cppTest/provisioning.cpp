#include <stdlib.h>
#include <iostream>
#include <sstream>
#include <stdexcept>
#include "mysql_connection.h"
#include <cppconn/driver.h>
#include <cppconn/exception.h>
#include <cppconn/resultset.h>
#include <cppconn/statement.h>
#include <cppconn/prepared_statement.h>

using namespace sql;

int main()
{
	std::cout << "Start c+ connect mysql test example \n";
	try {
		sql::Driver *myDriver;
		sql::Connection *myConn;
		sql::Statement *myStmt;
		sql::ResultSet *myRes;

		myDriver = get_driver_instance();
		myConn = myDriver->connect("tcp://127.0.0.1", "", "");
		myConn->setSchema("provisionning");

		std::string myQuery = "SELECT * FROM virtuals_machines";
		myStmt = myConn->createStatement();
		myRes = myStmt->executeQuery(myQuery);

		while(myRes->next())
		{
			std::cout <<myRes->getString("inbound_rule_ids") << std::endl;
		}
	}
	catch(SQLException &e)
	{
		std::cout << "exception";
	}
	return 0;
}
