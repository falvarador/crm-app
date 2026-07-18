namespace CrmApp.Data.Models;

public class Setting
{
    public int Id { get; set; }
    public string Key { get; set; } = string.Empty;
    public string Value { get; set; } = string.Empty;
    
    public DateTime CreatedOn { get; set; }
    public DateTime UpdatedOn { get; set; }
}