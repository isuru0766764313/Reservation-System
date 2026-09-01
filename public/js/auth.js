// Password matching validation
document.getElementById('signupForm').addEventListener('submit', function(e)
{
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirmPassword').value;
    const errorDiv = document.getElementById('passwordError');
    
    if(password !== confirmPassword)
    {
        e.preventDefault();
        errorDiv.classList.remove('d-none');
        document.getElementById('confirmPassword').focus();
    } else {
        errorDiv.classList.add('d-none');
    }
});