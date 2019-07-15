
import { Component, OnInit,Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef, MatDialog } from '@angular/material';
import { ActivatedRoute, Router } from '@angular/router';
// import { DatabaseService } from '../database.service';
// import { DialogService } from '../dialog.service';
import { ToastrManager } from 'ng6-toastr-notifications';
import { DatabaseService } from 'src/app/database.service';
@Component({
  selector: 'app-address-modal',
  templateUrl: './address-modal.component.html',
})
export class AddressModalComponent implements OnInit {

  constructor(public db:DatabaseService, public route:ActivatedRoute,@Inject(MAT_DIALOG_DATA) public data: any, private dialogRef: MatDialog,public toastr:ToastrManager) {
    console.log(this.data);
    this.get_countries();
    this.data.country=parseInt(this.data.country);
    console.log(this.data.pincode);
    
    if(this.data.country == '99'){
     this.get_states();
     this.get_district();
     this.get_city();
     this.get_pincode();
    }
   }

  ngOnInit() {
  }

  update_user(){
    console.log(this.data);
    this.db.getData(this.data,"User/update_user")
      .subscribe(resp=>{
        console.log(resp);  
        if(resp)
        {
          this.toastr.successToastr('Updated','Success!')
        }  
      });
    this.dialogRef.closeAll();
  }
  country_list:any=[];
  get_countries()
  {
    this.db.getData("","manufacturers/get_countries")
    .subscribe(resp=>{
      console.log(resp);
      this.country_list=resp;      
    });
  }
  state_list:any=[];
  get_states()
  {
    console.log(this.data);    
    if(this.data.country=='99'){   
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
    if(this.data.country=='99'){   
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
    if(this.data.country=='99'){   
      this.db.getData({"state":this.data.state,"district":this.data.district},"manufacturers/get_city")
      .subscribe(resp=>{
        console.log(resp);
        this.city_list=resp;
      })
    }    
  }
  pin_list:any=[];
  get_pincode(){
    if(this.data.country=='99'){   
      this.db.getData({"state":this.data.state,"district":this.data.district},"manufacturers/get_pincode")
      .subscribe(resp=>{
        console.log(resp);
        this.pin_list=resp;
      })
    }
  }
  
  onNoClick()
  {
    this.dialogRef.closeAll();
  }

}
