import {Component, OnInit, Renderer2} from '@angular/core';
import { SessionService } from '../session.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-header',
  templateUrl: './header.component.html',
})
export class HeaderComponent implements OnInit {
  
  constructor(private renderer: Renderer2,public session:SessionService,public route:Router) { }
  
   
  session_data:any={};
  abq_user:any={};
  ngOnInit() {
    this.session_data=this.session.GetSession();
    this.abq_user=this.session_data.value;
    console.log(this.abq_user.login_type);
    this.status = true;
  }
  status:boolean = false;
  toggleHeader() {
    console.log(this.status);
    
    // this.status = !this.status;
    if(this.status) {
      this.renderer.addClass(document.body, 'nav-active');
      this.status=false;
    }
    else {
      this.renderer.removeClass(document.body, 'nav-active');
      this.status=true;
    }
  }

  logout()
  {
    this.session.LogOutSession();
    this.route.navigate([""]); 
  }
}
