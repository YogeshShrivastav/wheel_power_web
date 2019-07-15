import { Component, OnInit, Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialog } from '@angular/material';
import { DatabaseService } from 'src/app/database.service';
import { ToastrManager } from 'ng6-toastr-notifications';

@Component({
  selector: 'app-customer-edit-dialog',
  templateUrl: './customer-edit-dialog.component.html',
})
export class CustomerEditDialogComponent implements OnInit {
  
  constructor(@Inject(MAT_DIALOG_DATA) public data:any,public dialogRef:MatDialog,public db:DatabaseService,public toastr:ToastrManager) { }
  
  ngOnInit() {
    this.get_states();
    this.get_district();
    this.get_city();
    this.get_pincode();
  }
  
  update_customer()
  {
    this.db.getData(this.data,"customer/update_customer")
    .subscribe(resp=>{
      console.log(resp);
      if(resp == 'success')
      {
        this.toastr.successToastr("Updates","Success!");
        this.dialogRef.closeAll();
      }
    })
  }
  
  onNoClick()
  {
    this.dialogRef.closeAll();
  }
  
  MobileNumber(event: any) {
    const pattern = /[0-9\+\-\ ]/;
    let inputChar = String.fromCharCode(event.charCode);
    if (event.keyCode != 8 && !pattern.test(inputChar)) {
      event.preventDefault();
    }
  }
  
  state_list:any=[];
  get_states()
  {
    console.log(this.data);    
    this.db.getData("","manufacturers/get_state")
    .subscribe(resp=>{
      console.log(resp);
      this.state_list=resp;
    });
  }
  district_list:any=[];
  get_district()
  {
    console.log(this.data.state);
    this.db.getData({"state":this.data.state},"manufacturers/get_district")
    .subscribe(resp=>{
      console.log(resp);
      this.district_list=resp;
    })
  }
  city_list:any=[];
  get_city()
  {
    console.log(this.data.state);
    console.log(this.data.district);
    this.db.getData({"state":this.data.state,"district":this.data.district},"manufacturers/get_city")
    .subscribe(resp=>{
      console.log(resp);
      this.city_list=resp;
    })
  }
  pin_list:any=[];
  get_pincode(){
    this.db.getData({"state":this.data.state,"district":this.data.district},"manufacturers/get_pincode")
    .subscribe(resp=>{
      console.log(resp);
      this.pin_list=resp;
    })
  }
  
}
