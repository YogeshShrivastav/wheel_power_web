import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';

@Component({
  selector: 'app-holiday-list',
  templateUrl: './holiday-list.component.html',
  animations: [slideToTop()]
})
export class HolidayListComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
