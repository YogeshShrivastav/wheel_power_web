import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';
import { DatabaseService } from 'src/app/database.service';
import { SessionService } from 'src/app/session.service';
import { DialogService } from 'src/app/dialog.service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-user-add',
  templateUrl: './user-add.component.html',
  animations: [slideToTop()]
  
})
export class UserAddComponent implements OnInit {
  
  constructor(public db:DatabaseService,public session:SessionService,public dialog:DialogService,public router:Router) {
    this.session_data=this.session.GetSession();
    this.abq_user=this.session_data.value
   }
  
  session_data:any={};
  abq_user:any={};
  data:any={};
  user_type:any="";
  loader:any='1';
  ngOnInit() {
    // this.get_states();
    this.get_countries();
  }
  
  
  active:any = {};
  toggleterritory(key,action)
  {
    console.log(action);
    console.log(key);
    
    if(action == 'open')
    { this.active[key] = true; }
    if(action == 'close')
    { this.active[key] = false;}

    console.log(this.active);
  }

  select_type()
  {
    console.log(this.user_type);
    if(this.user_type == 'SYSTEM USER')
    {
      this.data.admin_type="Admin";
    }
  }

  country_list:any=[];
  get_countries()
  {
    this.db.getData("","manufacturers/get_countries")
    .subscribe(resp=>{
      console.log(resp);
      this.loader = '';
      this.country_list=resp;
      this.data.country_id=99;
      this.get_states();
    });
  }
  state_list:any=[];
  get_states()
  {
    console.log(this.data);
    
    if(this.data.country_id=='99'){   
      this.db.getData("","manufacturers/get_state")
      .subscribe(resp=>{
        console.log(resp);
        this.state_list=resp;
      });
    }
  }
  district_list:any=[];
  get_district()
  {
    console.log(this.data.state);
    if(this.data.country_id=='99'){   
      this.db.getData({"state":this.data.state},"manufacturers/get_district")
      .subscribe(resp=>{
        console.log(resp);
        this.district_list=resp;
      })
   }
  }
  city_list:any=[];
  get_city()
  {
    console.log(this.data.state);
    console.log(this.data.district);
    if(this.data.country_id=='99'){   
      this.db.getData({"state":this.data.state,"district":this.data.district},"manufacturers/get_city")
      .subscribe(resp=>{
        console.log(resp);
        this.city_list=resp;
      })
    }    
  }
  pin_list:any=[];
  get_pincode(){
    if(this.data.country_id=='99'){   
      this.db.getData({"state":this.data.state,"district":this.data.district},"manufacturers/get_pincode")
      .subscribe(resp=>{
        console.log(resp);
        this.pin_list=resp;
      })
    }
  }

  MobileNumber(event: any) {
    const pattern = /[0-9\+\-\ ]/;
    let inputChar = String.fromCharCode(event.charCode);
    if (event.keyCode != 8 && !pattern.test(inputChar)) {
      event.preventDefault();
    }
  }

  saving:any=0;
  insert_user()
  {
    this.loader="1";
    if(this.user_type == 'SALES USER')
    {
      this.data.admin_type=this.user_type;
    }
    this.data.user_id=this.abq_user.id;
    console.log(this.data);
    if(this.saving == 0)
    {
      this.db.getData(this.data,"User/insert_user")
      .subscribe(resp=>{
        console.log(resp);
        if(resp)
        {
          this.dialog.success("Success","User Inserted");
          if(this.user_type == 'SALES USER'){
            this.router.navigate(["/sale-user-list"]);
          }
          else{
            this.router.navigate(["/system-user-list"]);
          }
        }
      })
      this.saving = 1;
    }
  }
}
