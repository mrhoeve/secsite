package nl.windesheim.somesite.docker;

import nl.windesheim.somesite.settings.Settings;
import org.junit.ClassRule;
import org.testcontainers.containers.GenericContainer;
import org.testcontainers.containers.Network;

import java.io.File;

public class TestWithinDocker {
	protected static final Network network = Network.newNetwork();
	
	@ClassRule
	protected static final GenericContainer mysqlContainer;
	
	@ClassRule
	protected static final GenericContainer wwwContainer;

	static {
		mysqlContainer =
				new GenericContainer("somesitesql:latest")
						.withCommand("--default-authentication-plugin=mysql_native_password")
						.withNetwork(network)
						.withNetworkAliases("mysql")
						.withExposedPorts(3306);
		
		mysqlContainer.start();
		
		wwwContainer =
				new GenericContainer("somesite:latest")
						.withNetwork(network)
						.withEnv("MYSQLSRV", "mysql")
						.withEnv("MYSQLSRVPRT", String.valueOf(mysqlContainer.getMappedPort(3306)))
						.withExposedPorts(80);
		
		wwwContainer.start();
		
		Settings.getInstance().setSettings(String.valueOf(wwwContainer.getMappedPort(80)), String.valueOf(mysqlContainer.getMappedPort(3306)));
	}
}
