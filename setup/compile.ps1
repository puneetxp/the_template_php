
php C:\xampp816\htdocs\tphp\intaxingnew\app\karl\setup\autosetup.php 2>&1 | Out-Host
Write-Host 'Change'
composer dump-autoload -o --working-dir=../../../
try
{
  $watcher = New-Object -TypeName System.IO.FileSystemWatcher -Property @{
    Path = $PSScriptRoot
    Filter = '*.php'
    IncludeSubdirectories = $true
    NotifyFilter = [IO.NotifyFilters]::FileName, [IO.NotifyFilters]::LastWrite 
  }
  $action = {
    php C:\xampp816\htdocs\tphp\intaxingnew\app\karl\setup\autosetup.php 2>&1 | Out-Host
    Write-Host 'Change'
    composer dump-autoload -o --working-dir=../../../
  }
  $handlers = . {
    Register-ObjectEvent -InputObject $watcher -EventName Changed  -Action $action 
    Register-ObjectEvent -InputObject $watcher -EventName Created  -Action $action 
    Register-ObjectEvent -InputObject $watcher -EventName Deleted  -Action $action 
    Register-ObjectEvent -InputObject $watcher -EventName Renamed  -Action $action 
  }
  $watcher.EnableRaisingEvents = $true
  Write-Host "Watching for changes to $Path"
  do
  {
    Wait-Event -Timeout 1
    Write-Host "." -NoNewline
  } while ($true)
}
finally
{
  $watcher.EnableRaisingEvents = $false
  $handlers | ForEach-Object {
    Unregister-Event -SourceIdentifier $_.Name
  }
  $handlers | Remove-Job
  $watcher.Dispose()
  Write-Warning "Event Handler disabled, monitoring ends."
}