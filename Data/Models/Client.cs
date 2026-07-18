using Microsoft.AspNetCore.Identity;

namespace CrmApp.Data.Models;

public class Client
{
    public int Id { get; set; }
    public string Name { get; set; } = string.Empty;
    public string Email { get; set; } = string.Empty;
    public string Phone { get; set; } = string.Empty;
    public string? Company { get; set; }
    public string? Notes { get; set; }
    public bool IsActive { get; set; }
    
    public string? UserId { get; set; }
    
    public required IdentityUser User { get; set; }
    
    public DateTime CreatedOn { get; set; }
    public DateTime UpdatedOn { get; set; }
}