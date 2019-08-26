/*
 * This file is part of the ekino/sonata project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

Health.Check = Em.Object.extend({
  checkName: null,
  message: null,
  status: false,
  service_id: null,

  icon: function() {
    if (this.status_name === "check_result_ok") {
      return "glyphicon glyphicon-ok-sign";
    }

    if (this.status_name === "check_result_warning") {
      return "glyphicon glyphicon-warning-sign";
    }

    if (this.status_name === "check_result_critical") {
      return "glyphicon glyphicon-fire";
    }

    if (this.status_name === "check_result_unknown") {
      return "glyphicon glyphicon-question-sign";
    }

    return "glyphicon glyphicon-exclamation-sign";
  }.property('status_name'),

  runUrl: function() {
    return api.liip_monitor_run_single_check.replace('replaceme', this.service_id);
  }.property('checkName')
});

