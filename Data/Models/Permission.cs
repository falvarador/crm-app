namespace CrmApp.Data.Models;

public class Permission
{
    public int Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public string Slug { get; set; } = string.Empty;
    
    public DateTime CreatedOn { get; set; }
    public DateTime UpdatedOn { get; set; }
    
    public bool IsDeleted { get; set; }
}