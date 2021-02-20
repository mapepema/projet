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
#include <thread>
#include <chrono>
#include <cmath>

using namespace sql;

int main()
{
	std::cout << "Start connection mysql\n";
	try {

		int seconds = 5;
		unsigned int milliseconds = seconds * 1000;

		sql::Driver *myDriver;
		sql::Connection *myConn;
		sql::Statement *myStmt;
		sql::ResultSet *myRes;

		myDriver = get_driver_instance();
		myConn = myDriver->connect("tcp://127.0.0.1", "", "");
		myConn->setSchema("provisionning");


		while(true){
			std::string myQuery = "SELECT * FROM virtuals_machines_states";
			myStmt = myConn->createStatement();
			myRes = myStmt->executeQuery(myQuery);

			while(myRes->next())
			{
				//foreach line get the id, and state. 
				//std::string id_virtual_machine = myRes->getString("id_virtual_machine");
				//std::string state = myRes->getString("state");
				int id_virtual_machine  = myRes->getInt("id_virtual_machine");
				int state = myRes->getInt("state");
				std::cout <<"Id de la machine : " << id_virtual_machine << ", State : "<< state << std::endl;
				
				//the number of ten should be use as an value of an loop over the action
				//to determine how many repetitions or the time need to performed action
				int step = ((state/10)%10)*10 + state%10;
				//the nomber of thousands should be use as a state of the debbug action
				int debug_state = (state/100)%10;
				//the main value of the state 
				int action = (state-step-debug_state*100)/1000;
				std::cout << "action : "  << action << ", debug : " << debug_state << ", step : " << step << std::endl;
				switch(action)
				{
					case 1:
					{
						switch(debug_state)
						{
							case 0: 
							{
								//if it the first execution then execute normaly 
								std::string command = "php test.php ";
								command.append(std::to_string(id_virtual_machine));
								command.append(" $");
								system(command.c_str());
								std::cout << std::endl;
								
							}
							break;
							case 1:
							{
								//first debug attempt
								switch(step)
								{
									//if no step was successful then correct from beginning 
									case 0:
									{
										std::string command = "php test.php ";
										command.append(std::to_string(id_virtual_machine));
										command.append(" $");
										system(command.c_str());
										std::cout << std::endl;
									}
									break;
									case 1: 
									break;
									default:
									break;
								}
							}
							break;
						}
					}
					break;
					case 2:
					{
						std::string command = "php test.php ";
						command.append(std::to_string(action));
						command.append(" $");
						system(command.c_str());
						std::cout << std::endl;
						
					}
					break;
					case 3:	
					{
						std::string command = "php test.php ";
						command.append(std::to_string(action));
						command.append(" $");
						system(command.c_str());
						std::cout << std::endl;
					}
					break;				
					case 4:	
					{
						std::string command = "php test.php ";
						command.append(std::to_string(action));
						command.append(" $");
						system(command.c_str());
						std::cout << std::endl;
					}
					break;				
					case 5:
					{
						std::string command = "php test.php ";
						command.append(std::to_string(action));
						command.append(" $");
						system(command.c_str());
						std::cout << std::endl;
					}
					break;
					case 6:
					{
						std::string command = "php test.php ";
						command.append(std::to_string(action));
						command.append(" $");
						system(command.c_str());
						std::cout << std::endl;	
					}
					break;
					default:
					break;
				}

				/*
				try
				{
					int id = std::stoi(id_virtual_machine);
					std::cout << id;
				}
				catch(...)
				{
					std::cerr << "Error \n";
				}*/
			}

			std::this_thread::sleep_for(std::chrono::milliseconds(milliseconds));

		}
	}
	catch(SQLException &e)
	{
		std::cout << "exception";
	}
	return 0;
}
