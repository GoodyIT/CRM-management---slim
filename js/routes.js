
routie({
    'dashboard/:timeInc': function(timeInc) {
			 $('#results').load(base_uri + 'api/dashboard/?timeIncrement='+timeInc);
    },
    'users': function() {
        $("#routeinfo").html("User information now");
    },
    'users/:name': function(name) {
        $("#routeinfo").html("User information now for " + name);
    },
    'lead': function() {
            $('#results').load(base_uri + 'api/leads');
    },
    'lead/create': function() {
        $('#results').load(base_uri + 'api/leads/create');
    },
    'lead/:params': function(params) {
            $('#results').load(base_uri + 'api/leads?' + params);
    },
    'lead/page/:pageId': function(pageId) {
            $('#results').load(base_uri + 'api/leads?leads_page=' + pageId);
    },
    'lead/page/:pageId/:params': function(pageId, params) {
            $('#results').load(base_uri + 'api/leads?leads_page=' + pageId + '&' + params);
    },
    'lead/edit/:personId': function(personId) {
         $('#results').load(base_uri + 'api/leads/edit/' + personId);
    },
    'lead/delete/:personId': function(personId) {
         $('#results').load(base_uri + 'api/leads/delete/' + personId);
    },
    'clients': function() {
            $('#results').load(base_uri + 'api/leads/clients');
    },
    'clients/:params': function(params) {
        $('#results').load(base_uri + 'api/leads/clients?' + params);
    },
    'clients/page/:pageId': function(pageId) {
            $('#results').load(base_uri + 'api/leads/clients?clients_page=' + pageId);
    },
    'policies': function() {
            $('#results').load(base_uri + 'api/leads/policies');
    },
    'policies/:params': function(params) {
            $('#results').load(base_uri + 'api/leads/policies?' + params);
    },
    'policies/page/:pageId': function(pageId) {
            $('#results').load(base_uri + 'api/leads/policies?policies_page=' + pageId);
    },
    'reports': function() {
			 $('#results').load(base_uri + 'api/reports');
    },
    'chat': function() {
			 $('#results').load(base_uri + 'api/chat');
    },
    'mail': function() {
			 $('#results').load(base_uri + 'api/mail');
    },
    'mail/page/:pageId': function(folder,pageId) {
			 $('#results').load(base_uri + 'api/mail?mail_page='+pageId);
    },
    'mail/folder/:folder/page/:pageId': function(folder,pageId) {
			 $('#results').load(base_uri + 'api/mail?folder='+folder+'&mail_page='+pageId);
    },
    'mail/folder/:folder/page/:pageId/search/:term': function(folder,pageId,term) {
			 $('#results').load(base_uri + 'api/mail?folder='+folder+'&mail_page='+pageId+'&term='+term);
    },
    'mail/folder/:folder': function(folder) {
			 $('#results').load(base_uri + 'api/mail?folder='+folder);
    },
    'mail/search/:terms': function(terms) {
			 $('#results').load(base_uri + 'api/mail');
    },
    'mail/view/:emailId': function(emailId) {
			 $('#results').load(base_uri + 'api/mail/view/'+emailId);
    },
    'mail/compose': function() {
			 $('#results').load(base_uri + 'api/mail/compose');
    },
    'mail/compose/:emailId': function(emailId) {
			 $('#results').load(base_uri + 'api/mail/compose?emailId='+emailId);
    },
    'calendar': function() {
			 $('#results').load(base_uri + 'api/calendar/render');
    },
    'news': function() {
			 $('#results').load(base_uri + 'api/news');
    },
    'news/sort/:sortId': function(sortID) {
			 $('#results').load(base_uri + 'api/news/sort/'+sortID);
    },
    'news/page/:pageId': function(pageId) {
			 $('#results').load(base_uri + 'api/news?news_page='+pageId);
    },
    'news/sort/:sortId/page/:pageId': function(sortId,pageId) {
			 $('#results').load(base_uri + 'api/news/sort/'+sortId+'?news_page='+pageId);
    },
    'news/view/:articleId': function(articleId) {
			 $('#results').load(base_uri + 'api/news/view/'+articleId);
    },
    'news/edit/:articleId': function(articleId) {
			 $('#results').load(base_uri + 'api/news/edit/'+articleId);
    },
    'news/create': function() {
			 $('#results').load(base_uri + 'api/news/new');
    },
    'admin/settings': function() {
        $('#results').load(base_uri + 'api/admin/settings');
    },
    'sms/templates': function() {
			 $('#results').load(base_uri + 'api/twilio/messageManager');
    },
    'admin/agencies': function() {
       $('#results').load(base_uri + 'api/admin/agencies');
    },
    'admin/agencies/create': function() {
       $('#results').load(base_uri + 'api/admin/agencies/create');
    },
    'admin/agencies/edit/:agencyId': function(agencyId) {
        $('#results').load(base_uri + 'api/admin/agencies/edit/' + agencyId);
    },
    'admin/usergroups': function() {
       $('#results').load(base_uri + 'api/admin/usergroups');
    },
    'admin/usergroups/create': function() {
        $('#results').load(base_uri + 'api/admin/usergroups/create');
    },
    'admin/usergroups/edit/:userGroupId': function(userGroupId) {
        $('#results').load(base_uri + 'api/admin/usergroups/edit/' + userGroupId);
    },
     'admin/leadsources': function() {
       $('#results').load(base_uri + 'api/admin/leadsources');
    },
    'admin/leadsources/edit/:leadsourceId': function(leadsourceId) {
        $('#results').load(base_uri + 'api/admin/leadsources/edit/' + leadsourceId);
    },
    'admin/user/list': function() {
        $('#results').load(base_uri + 'api/admin/user/list');
    },
    'admin/user/list/:state': function(state) {
        $('#results').load(base_uri + 'api/admin/user/list?state='+state);
    },
    'admin/user/create': function() {
        $('#results').load(base_uri + 'api/admin/user/create');
    },
    'admin/user/edit/:userId': function(userId) {
        $('#results').load(base_uri + 'api/admin/user/edit/' + userId);
    },
    'admin/sendinblue/templates': function() {
        $('#results').load(base_uri + 'api/admin/sendinblue/templates');
    },
    'admin/sendinblue/sms/templates': function() {
        $('#results').load(base_uri + 'api/admin/sendinblue/sms/templates');
    },
    'admin/sendinblue/sms/setting': function() {
        $('#results').load(base_uri + 'api/admin/sendinblue/sms/setting');
    },
    'admin/sendinblue/saveEmailTemplate': function() {
        $('#results').load(base_uri + 'api/admin/sendinblue/saveEmailTemplate');
    },
    'admin/carriers/list': function() {
        $('#results').load(base_uri + 'api/admin/carriers');
    },
    'admin/carriers/plans': function() {
        $('#results').load(base_uri + 'api/admin/plans');
    },
    'recordings/view/:personId': function(personId) {
        $('#results').load(base_uri + 'api/leads/recordingsview/'+personId);
    },
    'recordings/number/:number': function(number) {
        $('#results').load(base_uri + 'api/leads/recordingsnumber/'+number);
    },
    'recordings/search': function() {
        $('#results').load(base_uri + 'api/leads/number.php');
    },
    'issues': function() {
        $('#results').load(base_uri + 'api/issues');
    },
    'pelican': function() {
        $('#results').load(base_uri + 'api/leads/pelican.php');
    },
    'issues/create': function() {
        $('#results').load(base_uri + 'api/issues/create');
    },
    'issues/view/:issueId': function(issueId) {
        $('#results').load(base_uri + 'api/issues/view/'+issueId);
    },
    '*': function() {
        $(document).ready(function(){
            $.get(base_uri + 'api/dashboard', function (templateData) {
                var template=Handlebars.compile(templateData);
                $("#results").html(template());
            }, 'html');
        });
    }
});
//EXAMPLE OF JSON AND LOAD
// $(document).ready(function(){
//            $.ajax({url: "/quote_engine/jqueryajax/json.json", success: function(jsonData){
//                $.get(base_uri + 'api/leads/leadform.php', function (templateData) {
//                    var template=Handlebars.compile(templateData);
//                    $("#results").html(template(jsonData));
//                }, 'html');
//             }});
//        });