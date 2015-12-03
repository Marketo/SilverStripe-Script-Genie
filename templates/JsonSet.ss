<% if $RootObject != 'window' %>
var $RootObject = $RootObject || {};
<% end_if %>

$RootObject.$Type = [
<% loop $Items %>
$Me.AsJSON <% if not $Last %>,<% end_if %>
<% end_loop %>
];