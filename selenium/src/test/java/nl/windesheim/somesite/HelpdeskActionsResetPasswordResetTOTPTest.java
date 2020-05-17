package nl.windesheim.somesite;

import nl.windesheim.somesite.database.Database;
import nl.windesheim.somesite.dto.User;
import nl.windesheim.somesite.interactions.Interactions;
import nl.windesheim.somesite.useractions.Menu;
import nl.windesheim.somesite.useractions.admin.AdminChangePassword;
import nl.windesheim.somesite.useractions.admin.ResetTOTPSecret;
import nl.windesheim.somesite.useractions.admin.SelectUser;
import nl.windesheim.somesite.useractions.user.ChangePassword;
import nl.windesheim.somesite.useractions.user.Login;
import org.apache.commons.lang3.StringUtils;
import org.junit.jupiter.api.*;

import static nl.windesheim.somesite.useractions.UserActions.navigateTo;

@TestInstance(TestInstance.Lifecycle.PER_CLASS)
@TestMethodOrder(MethodOrderer.OrderAnnotation.class)
public class HelpdeskActionsResetPasswordResetTOTPTest {
	private final User helpdeskUser = new User("helpdesk", "WelcomeHelpdesk01");
	private final User regularUser = new User("testU", "Az09!@#$%&*(<>?)");
	
	@BeforeAll
	void setUp() {
		// User helpdesk heeft reset wachtwoord en reset TOTP secret rechten
		regularUser.setFaSecret("BBBRKQC5TQAZE4DT5LPQG63NZ6SUNV64");
		Database.getInstance().resetUserForLogin(helpdeskUser.getUsername(), helpdeskUser.getPassword(), false);
		Database.getInstance().deleteUserIfExists(regularUser.getUsername());
		Database.getInstance().createUser(regularUser);
	}
	
	@Order(1)
	@Test
	public void loginAsHelpdesk() {
		loginAsUser(helpdeskUser,true);
	}
	
	@Order(2)
	@Test
	public void assessBeheerAccounts() {
		Menu.selectAccountsAndClickOnBeheerAccounts();
		SelectUser.assertAvailableColumns(false, false, false, false, false, true, true, false, false);
	}
	
	@Order(3)
	@Test
	public void resetUserPassword() {
		String newPassword="Welcome01";
		SelectUser.clickOnChangePasswordButton(regularUser.getUsername());
		Assertions.assertFalse(AdminChangePassword.getCurrentStateOfChangePwONL());
		
		AdminChangePassword.fillPassword(newPassword);
		AdminChangePassword.setChangePwONL(true);
		AdminChangePassword.clickOnSave();
		AdminChangePassword.assertSuccess(true);
		AdminChangePassword.clickOnBackToSelect();
		regularUser.setPassword(newPassword);
		
		Menu.selectCurrentUserAndClickOnUitloggen();
		
		loginAsUser(regularUser,false);
		mandatoryChangePasswordOfUser(regularUser, "Az09!@#$%&*(<>?)");
		
		Menu.selectCurrentUserAndClickOnUitloggen();
		loginAsUser(helpdeskUser, true);
	}
	
	@Order(4)
	@Test
	public void resetTotpSecret() {
		Menu.selectAccountsAndClickOnBeheerAccounts();
		SelectUser.clickOnReset2FAButton(regularUser.getUsername());
		ResetTOTPSecret.clickOnRemove2FA();
		ResetTOTPSecret.assertSuccess(true);
		ResetTOTPSecret.clickOnBackToSelect();
		regularUser.setFaSecret(null);
		
		Menu.selectCurrentUserAndClickOnUitloggen();
		
		loginAsUser(regularUser,true);
		Menu.selectCurrentUserAndClickOnUitloggen();
	}
	
	private void loginAsUser(User logInAsUser, Boolean assertSuccess) {
		navigateTo("index.php");
		Menu.clickOnLogin();
		
		Login.fillCredentials(logInAsUser.getUsername(), logInAsUser.getPassword());
		if(!StringUtils.isEmpty(logInAsUser.getFaSecret())) {
			ChangePassword.fill2FACode(Interactions.calculate2FACode(logInAsUser.getFaSecret()));
		}
		Login.clickOnLoginButton();
		if (assertSuccess) Login.assertSuccessfulLogin();
	}
	
	private void mandatoryChangePasswordOfUser(User changeForUser, String newPassword) {
		ChangePassword.assertMustChange(true);
		ChangePassword.fillCurrentPassword(changeForUser.getPassword());
		ChangePassword.fillFirstNewPassword(newPassword);
		ChangePassword.fillSecondNewPassword(newPassword);
		if(!StringUtils.isEmpty(changeForUser.getFaSecret())) {
			ChangePassword.fill2FACode(Interactions.calculate2FACode(changeForUser.getFaSecret()));
		}
		ChangePassword.clickOnSubmitButton();
		ChangePassword.assertMustChange(false);
		ChangePassword.assertError(false);
		ChangePassword.assertSuccess(true);
		
		changeForUser.setPassword(newPassword);
		
		ChangePassword.clickOnBackToIndexButton();
	}
}