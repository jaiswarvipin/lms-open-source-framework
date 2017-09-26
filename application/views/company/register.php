<div class="section"></div>
  <main>
    <center>
      <img class="responsive-img" src="<?php echo SITE_URL.DEFAULT_LOGO?>" width='100px' />
      <div class="section"></div>

      <h5 class="indigo-text">Register, your company account</h5>
      <div class="section"></div>

      <div class="container">
        <div class="z-depth-1 grey lighten-4 row" style="display: inline-block; padding: 32px 48px 0px 48px; border: 1px solid #EEE;">

          <form class="col s12" method="post" action="<?php echo SITE_URL?>company/register/doRegistration" name="frmCompanyRegistration" id="frmCompanyRegistration">
            <div class='row'>
              <div class='col s14'>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='txtCompanyName' id='txtCompanyName' />
                <label for='txtCompanyName'>Enter your company name</label>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='text' name='txtAdminName' id='txtAdminName' />
                <label for='txtAdminName'>Enter your name</label>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='email' name='txtEmail' id='txtEmail' />
                <label for='txtEmail'>Enter your email</label>
              </div>
            </div>

            <div class='row'>
              <div class='input-field col s12'>
                <input class='validate' type='password' name='txtPassword' id='txtPassword' />
                <label for='txtPassword'>Enter your password</label>
              </div>
              <label style='float: right;'>
								<a class='pink-text' href='javascript:void(0);'><b>Forgot Password?</b></a>
							</label>
            </div>
            
            <br />
            <center>
              <div class='row'>
                <button name='cmdCompanyRegister' id='cmdCompanyRegister' class='col s12 btn btn-large waves-effect indigo' formName="frmCompanyRegistration">Register<?php echo getLoaderHTML()?></button>
              </div>
            </center>
          </form>
        </div>
      </div>
      <a href="<?php echo SITE_URL?>login">Back to Login</a>
    </center>

    <div class="section"></div>
    <div class="section"></div>
  </main>