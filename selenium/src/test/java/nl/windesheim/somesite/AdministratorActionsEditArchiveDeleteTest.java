package nl.windesheim.somesite;

import nl.windesheim.somesite.database.Database;
import nl.windesheim.somesite.dto.User;
import nl.windesheim.somesite.interactions.Interactions;
import nl.windesheim.somesite.useractions.Menu;
import nl.windesheim.somesite.useractions.admin.*;
import nl.windesheim.somesite.useractions.user.ArchivedUser;
import nl.windesheim.somesite.useractions.user.ChangePassword;
import nl.windesheim.somesite.useractions.user.Enable2FA;
import nl.windesheim.somesite.useractions.user.Login;
import org.junit.Before;
import org.junit.jupiter.api.*;

import static nl.windesheim.somesite.useractions.UserActions.navigateTo;

@TestInstance(TestInstance.Lifecycle.PER_CLASS)
@TestMethodOrder(MethodOrderer.OrderAnnotation.class)
public class AdministratorActionsEditArchiveDeleteTest {
	private final User adminUser = new User("admin", "WelcomeAdmin01");
	private final User regularUser = new User("testU");
	
	@BeforeAll
	void setUp() {
		// User admin heeft Nieuw, Bewerk, Archiveer en Verwijder rechten
		Database.getInstance().resetUserForLogin(adminUser.getUsername(), adminUser.getPassword(), true);
		Database.getInstance().deleteUserIfExists(regularUser.getUsername());
	}
	
	@Order(1)
	@Test
	public void loginAsAdminAndMandatoryChangePassword() {
		String goodNewPassword = "Az09!@#$%&*()<>?";
		
		loginAsUser(adminUser,false);
		mandatoryChangePasswordOfUser(adminUser, goodNewPassword);
	}
	
	@Order(2)
	@Test
	public void assessBeheerAccounts() {
		Menu.selectAccountsAndClickOnBeheerAccounts();
		SelectUser.assertAvailableColumns(true, true, true, true, true, false, false, true, true);
	}
	
	@Order(3)
	@Test
	public void createUser() {
		Menu.selectAccountsAndClickOnMaakNieuwAccountAan();
		String wrongUsername = "test";
		String wrongEmail = "test@test";
		String rightEmail = "test@test.com";
		String normallyNotStrongEnoughPassword = "Az!@#$%&*()";
		
		// Test verkeerde username
		AdminCreateNewUser.fillUsername(wrongUsername);
		AdminCreateNewUser.fillFirstname("first name");
		AdminCreateNewUser.fillEmail(rightEmail);
		AdminCreateNewUser.fillNewPassword(normallyNotStrongEnoughPassword);
		AdminCreateNewUser.clickOnSubmitButton();
		AdminCreateNewUser.assertError(true);
		AdminCreateNewUser.assertSuccess(false);
		
		// Test verkeerd emailadres
		AdminCreateNewUser.fillUsername(regularUser.getUsername());
		AdminCreateNewUser.fillFirstname("first name");
		AdminCreateNewUser.fillEmail(wrongEmail);
		AdminCreateNewUser.fillNewPassword(normallyNotStrongEnoughPassword);
		AdminCreateNewUser.clickOnSubmitButton();
		AdminCreateNewUser.assertError(true);
		AdminCreateNewUser.assertSuccess(false);
		
		AdminCreateNewUser.fillUsername(regularUser.getUsername());
		AdminCreateNewUser.fillFirstname("first name");
		AdminCreateNewUser.fillEmail(rightEmail);
		AdminCreateNewUser.fillNewPassword(normallyNotStrongEnoughPassword);
		AdminCreateNewUser.clickOnSubmitButton();
		AdminCreateNewUser.assertError(false);
		AdminCreateNewUser.assertSuccess(true);
		
		AdminCreateNewUser.clickOnBackToIndexButton();

		regularUser.setPassword(normallyNotStrongEnoughPassword);
		Assertions.assertTrue(SelectUser.isUserPresent(regularUser.getUsername()));
	}
	
	@Order(4)
	@Test
	public void loginAsNewlyCreatedUser() {
		Menu.selectCurrentUserAndClickOnUitloggen();
		navigateTo("index.php");
		Menu.clickOnLogin();
		Login.fillCredentials(regularUser.getUsername(), regularUser.getPassword());
		Login.clickOnLoginButton();
		Login.assertError(false);
		Login.assertSuccessfulLogin();
		
		Menu.selectCurrentUserAndClickOnUitloggen();
		loginAsUser(adminUser, true);
	}
	
	@Order(5)
	@Test
	public void editUser() {
		String goodNewPassword = "Az90!@#$%&*()<>?";
		
		Menu.selectAccountsAndClickOnBeheerAccounts();
		SelectUser.clickOnEditUserButton(regularUser.getUsername());
		EditUser.setChangePwONL(true);
		EditUser.setChangeArchivedAccount(false);
		EditUser.clickOnSave();
		EditUser.assertSuccess(true);
		Assertions.assertTrue(EditUser.getCurrentStateOfChangePwONL());
		Assertions.assertFalse(EditUser.getCurrentStateOfArchivedAccount());
		
		Menu.selectCurrentUserAndClickOnUitloggen();
		
		loginAsUser(regularUser, false);
		mandatoryChangePasswordOfUser(regularUser, goodNewPassword);
		
		Menu.selectCurrentUserAndClickOnUitloggen();
		loginAsUser(adminUser, true);
	}
	
	@Order(6)
	@Test
	public void archiveUser() {
		Menu.selectAccountsAndClickOnBeheerAccounts();
		SelectUser.clickOnArchiveUserButton(regularUser.getUsername());
		Assertions.assertFalse(ArchiveUser.getCurrentStateOfArchivedAccount());
		ArchiveUser.setChangeArchivedAccount(true);
		ArchiveUser.clickOnSave();
		Assertions.assertTrue(ArchiveUser.getCurrentStateOfArchivedAccount());
		ArchiveUser.clickOnBackToSelect();
		
		SelectUser.clickOnEditUserButton(regularUser.getUsername());
		Assertions.assertTrue(EditUser.getCurrentStateOfArchivedAccount());
		
		Menu.selectCurrentUserAndClickOnUitloggen();
		loginAsUser(regularUser, false);
		
		ArchivedUser.assertIsArchived(true);
		ArchivedUser.clickOnBackToIndex();
		
		loginAsUser(adminUser, true);
	}
	
	@Order(7)
	@Test
	public void removeUser() {
		Menu.selectAccountsAndClickOnBeheerAccounts();
		Assertions.assertTrue(SelectUser.isUserPresent(regularUser.getUsername()));
		
		SelectUser.clickOnRemoveUserButton(regularUser.getUsername());
		RemoveUser.clickOnRemoveUser();
		RemoveUser.assertSuccess(true);
		RemoveUser.clickOnBackToSelect();
		
		Assertions.assertFalse(SelectUser.isUserPresent(regularUser.getUsername()));
		
		Menu.selectCurrentUserAndClickOnUitloggen();
	}
	
	private void loginAsUser(User logInAsUser, Boolean assertSuccess) {
		navigateTo("index.php");
		Menu.clickOnLogin();
		
		Login.fillCredentials(logInAsUser.getUsername(), logInAsUser.getPassword());
		Login.clickOnLoginButton();
		if (assertSuccess) Login.assertSuccessfulLogin();
	}
	
	private void mandatoryChangePasswordOfUser(User changeForUser, String newPassword) {
		ChangePassword.assertMustChange(true);
		ChangePassword.fillCurrentPassword(changeForUser.getPassword());
		ChangePassword.fillFirstNewPassword(newPassword);
		ChangePassword.fillSecondNewPassword(newPassword);
		ChangePassword.clickOnSubmitButton();
		ChangePassword.assertMustChange(false);
		ChangePassword.assertError(false);
		ChangePassword.assertSuccess(true);
		
		changeForUser.setPassword(newPassword);
		
		ChangePassword.clickOnBackToIndexButton();
	}
}