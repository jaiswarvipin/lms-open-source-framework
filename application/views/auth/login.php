<div class="section"></div>
  <main>
    <center>
      <img class="responsive-img" src="<?php echo SITE_URL.DEFAULT_LOGO?>" width='100px' />
      <div class="section"></div>

      <h5 class="indigo-text">Please, login into your account</h5>
      <div class="section divMsg warning"></div>

      <div class="container">
        <div class="z-depth-1 grey lighten-4 row" style="display: inline-block; padding: 32px 48px 0px 48px; border: 1px solid #EEE;">

          <form class="col s12" method="post" action="<?php echo SITE_URL?>login/doAuthincation" name="frmAuthencation" id="frmAuthencation">
            <div class='row'>
              <div class='col s12'>
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
								<a class='pink-text' href='#!'><b>Forgot Password?</b></a>
							</label>
            </div>

            <br />
            <center>
              <div class='row'>
                <button name='cmdLogin' id='cmdLogin' class='col s12 btn btn-large waves-effect indigo' formName="frmAuthencation">Login</button>
              </div>
            </center>
          </form>
        </div>
      </div>
      <a href="<?php echo SITE_URL?>company/register">Register Company</a>
    </center>

    <div class="section"></div>
    <div class="section"></div>
  </main>