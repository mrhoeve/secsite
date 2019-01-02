var firstPasswordElement = document.getElementById('password');
if(typeof(firstPasswordElement) != 'undefined' && firstPasswordElement != null) {
    firstPasswordElement.addEventListener('keyup', function () {
        var strengthBar = document.getElementById('strength');
        var strengthScore = 0;
        var lengthScore = 0;
        var passValue = firstPasswordElement.value;
        if (passValue.match(/[a-zA-Z0-9][a-zA-Z0-9]+/)) {
            strengthScore += 10;
        }
        if (passValue.match(/[~<>?]+/)) {
            strengthScore += 15;
        }
        if (passValue.match(/[!@#$%^&*()]+/)) {
            strengthScore += 15;
        }
        if (passValue.length > 8) {
            lengthScore = (passValue.length - 8) * 5;
            if (lengthScore > 60)
                lengthScore = 60;
            strengthScore += lengthScore;
        }
        // Remove the classes
        strengthBar.classList.remove('weak', 'lowmedium', 'highmedium', 'lowgood', 'good');
        // Apply correct class
        // We're not using switch but if, explanation here: https://stackoverflow.com/questions/6665997/switch-statement-for-greater-than-less-than
        if (strengthScore < 21) {
            strengthBar.classList.add('weak');
        } else if (strengthScore < 51) {
            strengthBar.classList.add('lowmedium');
        } else if (strengthScore < 71) {
            strengthBar.classList.add('highmedium');
        } else if (strengthScore < 86) {
            strengthBar.classList.add('lowgood');
        } else {
            strengthBar.classList.add('good');
        }
        strengthBar.value = strengthScore;
    });
}
