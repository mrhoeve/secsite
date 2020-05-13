package nl.windesheim.somesite.docker;

import org.testcontainers.containers.GenericContainer;

public class KGenericContainer extends GenericContainer<KGenericContainer> {
	public KGenericContainer(String containerName) {
		super(containerName);
	}
}
