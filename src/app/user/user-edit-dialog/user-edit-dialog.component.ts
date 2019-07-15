import { Component, OnInit,Inject } from '@angular/core';
import { MAT_DIALOG_DATA, MatDialogRef, MatDialog } from '@angular/material';
import { ActivatedRoute, Router } from '@angular/router';
// import { DatabaseService } from '../database.service';
// import { DialogService } from '../dialog.service';
import { ToastrManager } from 'ng6-toastr-notifications';
import { DatabaseService } from 'src/app/database.service';

@Component({
  selector: 'app-user-edit-dialog',
  templateUrl: './user-edit-dialog.component.html'
})
export class UserEditDialogComponent implements OnInit {

  constructor(public db:DatabaseService, public route:ActivatedRoute,@Inject(MAT_DIALOG_DATA) public data: any, private dialogRef: MatDialog,public toastr:ToastrManager) {
    console.log('dialog data');
    console.log(this.data);
    console.log(this.data.edit_item);
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

}
