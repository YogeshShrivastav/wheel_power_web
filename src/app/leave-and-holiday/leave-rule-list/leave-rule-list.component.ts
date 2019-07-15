import { Component, OnInit } from '@angular/core';
import { slideToTop } from '../../router-animation/router-animation.component';

@Component({
  selector: 'app-leave-rule-list',
  templateUrl: './leave-rule-list.component.html',
  animations: [slideToTop()]
})
export class LeaveRuleListComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

}
