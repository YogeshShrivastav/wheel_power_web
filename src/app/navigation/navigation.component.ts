import { Component, OnInit, Renderer2 } from '@angular/core';
import { SessionService } from '../session.service';

@Component({
  selector: 'app-navigation',
  templateUrl: './navigation.component.html',
})
export class NavigationComponent implements OnInit {
  
  constructor(private renderer: Renderer2,public session:SessionService) { }
  
  session_data:any={};
  abq_user:any={};
  ngOnInit() {
    this.session_data=this.session.GetSession();
    this.abq_user=this.session_data.value;
    console.log(this.abq_user.login_type);
    
    this.abq_user.login_type
  }
  
  status:boolean = false;
  toggleDropdown() {
    this.status = !this.status;
    
    if(this.status) {
      console.log('true');
      
      this.renderer.addClass(event.target, 'active');
      this.renderer.removeClass(document.body, 'active');
    }
    else {
      console.log('false');

      this.renderer.removeClass(event.target, 'active');
      this.renderer.removeClass(document.body, 'active');
    }
  }
  
  
  status1:boolean = false;
  toggleNav() {
    this.status1 = !this.status1;
    if(this.status1) {
      this.renderer.addClass(document.body, 'active');
    }
    else {
      this.renderer.removeClass(document.body, 'active');
    }
  }
  
  toggleHeader()
  {
    this.renderer.removeClass(document.body, 'nav-active');
  }
  
  false_toggle()
  {
    this.renderer.removeClass(document.getElementById("stock"), 'active');
  }
}
