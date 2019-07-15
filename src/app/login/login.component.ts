import { Component, OnInit } from '@angular/core';
import { slideToRight } from '../router-animation/router-animation.component';
import { SessionService } from '../session.service';
@Component({
  selector: 'app-login',
  templateUrl: './login.component.html',
  animations: [slideToRight()]
})
export class LoginComponent implements OnInit {

  constructor(public session:SessionService) { }
  data:any={};
  ngOnInit() {
  }

  login_user()
  {
    console.log(this.data);
    this.session.setSession(this.data);
    
  }

}
