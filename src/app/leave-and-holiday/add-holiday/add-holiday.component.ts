import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';

@Component({
  selector: 'app-add-holiday',
  templateUrl: './add-holiday.component.html',
  animations: [slideToTop()]

})
export class AddHolidayComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
