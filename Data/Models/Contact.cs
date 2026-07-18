namespace CrmApp.Data.Models;

public class Contact
{
    public int Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public string Email { get; set; } = string.Empty;
    public string Phone { get; set; } = string.Empty;
    public string? Position { get; set; }
    public string? Notes { get; set; }
    public bool IsActive { get; set; }
    
    public string? ClientId { get; set; }
    
    public required Client Client { get; set; }
    
    public DateTime CreatedOn { get; set; }
    public DateTime UpdatedOn { get; set; }
}