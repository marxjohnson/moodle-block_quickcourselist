
function quickcoursesearch(course){
	var progress = YAHOO.util.Dom.get('quickcourseprogress');
    var course = YAHOO.util.Dom.get('quickcourselistsearch').value;
    var quickcourselist = YAHOO.util.Dom.get('quickcourselist');
    if(xhr != undefined) {
        YAHOO.util.Connect.abort(xhr);
    }
    progress.style.visibility = 'visible';
    xhr = YAHOO.util.Connect.asyncRequest(
        'get',
        wwwroot+'/blocks/quickcourselist/quickcourse.php?instanceid='+instanceid+'&course='+course,
        {
            success: function(o) {
                progress.style.visibility = 'hidden';
                quickcourselist.innerHTML = o.responseText;
            },
            failure: function(o) {
                if(o.status == 0) {
                    progress.style.visibility = 'hidden';
                    quickcourselist.innerHTML = o.statusText;
                }
            }
       }
   );
}