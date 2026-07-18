namespace CrmApp.Data.Models;

public class TypeFollowUp
{
    public int Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public string Color { get; set; } = string.Empty;
    
    public DateTime CreatedOn { get; set; }
    public DateTime UpdatedOn { get; set; }
    
    public bool IsDeleted { get; set; }
}